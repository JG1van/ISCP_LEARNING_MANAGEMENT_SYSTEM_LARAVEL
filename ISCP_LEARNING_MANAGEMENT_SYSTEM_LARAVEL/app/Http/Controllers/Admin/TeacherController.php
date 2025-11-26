<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

use Illuminate\Support\Facades\File;


class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     *   Tampilkan daftar guru
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
     *   Simpan data guru baru (password default otomatis)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
            $imgPath = null;
            if ($request->hasFile('img')) {
                $imgPath = $request->file('img')->store('public/guru');
                $imgPath = str_replace('public/', '', $imgPath);
            }

            $teacher = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make('Guru1234'), //   default password
                'role' => 1, // Guru
                'email' => $request->email,
                'address' => $request->address,
                'phone' => $request->phone,
                'img' => $imgPath,
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
     *   Ambil data guru berdasarkan ID
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
     *   Update data guru
     */
    public function update(Request $request, $id)
    {
        $teacher = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:100|unique:users,username,' . $id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|in:0,1',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        //   Update data dasar
        $teacher->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
        ]);

        //   Upload foto baru jika ada
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($teacher->img && file_exists(public_path('images/users/' . $teacher->img))) {
                unlink(public_path('images/users/' . $teacher->img));
            }

            // Buat nama file baru berbasis username
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filename = 'foto-' . $request->username . '.' . $extension;

            // Simpan ke folder public/images/users
            $request->file('photo')->move(public_path('images/users'), $filename);

            // Simpan ke database
            $teacher->img = $filename;
            $teacher->save();
        }

        return response()->json(['success' => true, 'message' => 'Data guru berhasil diperbarui.']);
    }


    /**
     *   Hapus guru
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

        //   Cek apakah guru masih digunakan di tabel lain
        $relatedData = [];

        if (\App\Models\Student::where('user_id', $id)->exists()) {
            $relatedData[] = 'Siswa';
        }
        if (\App\Models\Serial::where('user_id', $id)->exists()) {
            $relatedData[] = 'serial';
        }
        if (\App\Models\ExerciseItem::where('user_id', $id)->exists()) {
            $relatedData[] = 'exercise_items';
        }

        // Jika masih ada relasi aktif, tolak penghapusan
        if (!empty($relatedData)) {
            $list = implode(', ', $relatedData);
            return response()->json([
                'success' => false,
                'message' => "Guru tidak dapat dihapus karena masih terhubung dengan data: {$list}.",
            ], 409);
        }

        try {
            //   Hapus foto jika ada dan filenya eksis di public/images/users
            if ($teacher->img) {
                $imagePath = public_path('images/users/' . $teacher->img);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            //   Hapus guru dari database
            $teacher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            //   Tangani error foreign key langsung dari database
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
     *   Reset Password Guru
     */
    public function resetPassword($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan.'
                ]);
            }

            // Password default baru
            $user->password = bcrypt('Guru1234');
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset menjadi: Guru1234'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset password. ' . $e->getMessage()
            ]);
        }
    }

}
