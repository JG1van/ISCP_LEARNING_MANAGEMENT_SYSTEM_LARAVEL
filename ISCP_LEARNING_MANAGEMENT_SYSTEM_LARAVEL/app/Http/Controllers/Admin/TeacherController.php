<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class TeacherController extends Controller
{
    public const ALLOWED_ROLES = [1, 2, 3];
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     *  Tampilkan daftar guru
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $teachers = User::orderBy('id', 'asc')->get();
            return response()->json(['data' => $teachers]);
        }

        $teachers = User::orderBy('id', 'asc')->get();
        return view('admin.guru.index', compact('teachers'));
    }

    /**
     *  Simpan guru baru (password default otomatis)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Upload foto jika ada
            $imgPath = null;
            if ($request->hasFile('img')) {
                $imgPath = $request->file('img')->store('users', 'public');
            }

            $teacher = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make('Guru1234'),
                'role' => 1, // Guru
                'email' => $request->email,
                'address' => $request->address,
                'phone' => $request->phone,
                'img' => $imgPath ? basename($imgPath) : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil ditambahkan.',
                'data' => $teacher,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan guru: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     *  Ambil data guru berdasarkan ID
     */
    public function edit($id)
    {
        $teacher = User::find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $teacher,
        ]);
    }

    /**
     *  Update data guru
     */
    public function update(Request $request, $id)
    {
        $teacher = User::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:100',
                'username' => 'required|string|max:100|unique:users,username,' . $id,
                'email' => 'nullable|email|max:100',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'role' => 'required|in:0,1',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ],
            [
                'name.required' => 'Nama wajib diisi.',
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Update data
        $teacher->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
        ]);

        // Upload foto jika ada
        if ($request->hasFile('photo')) {
            // Hapus foto lama
            if ($teacher->img && Storage::disk('public')->exists('users/' . $teacher->img)) {
                Storage::disk('public')->delete('users/' . $teacher->img);
            }

            $path = $request->file('photo')->store('users', 'public');
            $teacher->img = basename($path);
            $teacher->save();
        }

        return response()->json(['success' => true, 'message' => 'Data guru berhasil diperbarui.']);
    }

    /**
     *  Hapus guru
     */
    public function destroy($id)
    {
        $teacher = User::find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak ditemukan.',
            ], 404);
        }

        // Daftar relasi (format PATOKAN)
        $relations = [
            ['model' => \App\Models\Student::class, 'column' => 'user_id', 'label' => 'siswa'],
            ['model' => \App\Models\Serial::class, 'column' => 'user_id', 'label' => 'serial'],
            ['model' => \App\Models\ExerciseItem::class, 'column' => 'user_id', 'label' => 'soal'],
        ];

        $relatedData = [];

        foreach ($relations as $relation) {
            if ($relation['model']::where($relation['column'], $id)->exists()) {
                $relatedData[] = $relation['label'];
            }
        }

        if (!empty($relatedData)) {
            $list = implode(', ', $relatedData);
            return response()->json([
                'success' => false,
                'message' => "Guru tidak dapat dihapus karena masih terhubung dengan data: {$list}.",
            ], 409);
        }

        try {
            // Hapus foto
            if ($teacher->img && Storage::disk('public')->exists('users/' . $teacher->img)) {
                Storage::disk('public')->delete('users/' . $teacher->img);
            }

            $teacher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Guru tidak dapat dihapus karena masih terhubung dengan data lain.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus guru: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     *  Reset password guru
     */
    public function resetPassword($id)
    {
        try {
            $teacher = User::find($id);

            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan.',
                ]);
            }

            $teacher->password = bcrypt('Guru1234');
            $teacher->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset menjadi: Guru1234',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset password. ' . $e->getMessage(),
            ]);
        }
    }
}
