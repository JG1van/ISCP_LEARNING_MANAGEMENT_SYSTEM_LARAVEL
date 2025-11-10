<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Admin;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     *  Tampilkan daftar admin
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $admins = Admin::orderBy('id', 'asc')->get();
            return response()->json(['data' => $admins]);
        }

        $admins = Admin::orderBy('id', 'asc')->get();
        return view('admin.admin.index', compact('admins'));
    }

    /**
     *  Simpan data admin baru (password default otomatis)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:admins,username',
            'role' => 'nullable|integer|min:0|max:9',
            'date_in' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:20',
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
            // Ambil ID terakhir untuk manual increment (optional, seperti di TeacherController)
            $lastId = Admin::max('id');
            $newId = $lastId ? $lastId + 1 : 1;

            // Upload foto jika ada
            $imgPath = null;
            if ($request->hasFile('img')) {
                $imgPath = $request->file('img')->store('public/admins');
                $imgPath = str_replace('public/', '', $imgPath);
            }

            $admin = Admin::create([
                'id' => $newId,
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make('Admin1234'),
                'role' => $request->role ?? 0, // default 0 jika kosong
                'date_in' => $request->date_in,
                'position' => $request->position,
                'phone' => $request->phone,
                'img' => $imgPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil ditambahkan (password default: Admin1234).',
                'data' => $admin,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan admin: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     *  Ambil data admin berdasarkan ID
     */
    public function edit($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $admin,
        ]);
    }

    /**
     *  Update data admin
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:100',
                'username' => 'required|string|max:50|unique:admins,username,' . $id,
                'role' => 'nullable|integer|min:0|max:9',
                'date_in' => 'nullable|date',
                'position' => 'nullable|string|max:50',
                'phone' => 'nullable|string|max:20',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ],
            [
                'name.required' => 'Nama wajib diisi.',
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
                'role.integer' => 'Role harus berupa angka.',
                'role.min' => 'Role tidak boleh kurang dari 0.',
                'role.max' => 'Role tidak boleh lebih dari 9.',
                'date_in.date' => 'Format tanggal tidak valid.',
                'position.max' => 'Jabatan maksimal 50 karakter.',
                'phone.max' => 'Nomor telepon maksimal 20 karakter.',
                'photo.image' => 'File foto harus berupa gambar.',
                'photo.mimes' => 'Format foto harus jpeg, png, jpg, atau webp.',
                'photo.max' => 'Ukuran foto maksimal 2MB.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Update data admin
        $admin->update([
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role ?? 0,
            'date_in' => $request->date_in,
            'position' => $request->position,
            'phone' => $request->phone,
        ]);

        // Upload foto jika ada
        if ($request->hasFile('photo')) {

            // Hapus foto lama jika ada
            if (!empty($admin->img)) {
                $oldFile = public_path('images/admins/' . $admin->img);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            // Simpan foto baru
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filename = 'foto-' . $request->username . '-' . time() . '.' . $extension;
            $request->file('photo')->move(public_path('images/admins'), $filename);

            $admin->img = $filename;
            $admin->save();
        }

        return response()->json(['success' => true, 'message' => 'Data admin berhasil diperbarui.']);
    }

    /**
     *  Hapus admin
     */
    public function destroy($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak ditemukan.',
            ], 404);
        }


        $relatedData = [];

        // Contoh: jika ada model Lesson yang punya kolom 'admin_id'
        if (\App\Models\LessonItem::where('admin_id', $id)->exists()) {
            $relatedData[] = 'materi';
        }
        if (\App\Models\ExerciseItem::where('admin_id', $id)->exists()) {
            $relatedData[] = 'soal';
        }


        // Jika admin masih punya relasi aktif di salah satu tabel
        if (!empty($relatedData)) {
            $list = implode(', ', $relatedData);
            return response()->json([
                'success' => false,
                'message' => "Admin ini tidak dapat dihapus karena masih terhubung dengan data: {$list}.",
            ], 409);
        }

        try {
            //   Hapus gambar admin jika ada
            if ($admin->img) {
                $imagePath = public_path('images/admins/' . $admin->img);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $admin->delete();

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil dihapus.',
            ]);
        } catch (QueryException $e) {

            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin tidak dapat dihapus karena masih terhubung dengan data lain.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus admin: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     *  Reset password Admin
     */
    public function resetPassword($id)
    {
        try {
            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data admin tidak ditemukan.',
                ]);
            }

            $admin->password = bcrypt('Admin1234');
            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset menjadi: Admin1234',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset password. ' . $e->getMessage(),
            ]);
        }
    }
}
