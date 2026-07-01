<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;

class AdminProfilController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * 🔹 Tampilkan halaman profil admin yang sedang login.
     */
    public function index()
    {
        $admin = Auth::user();
        return view('admin.pengaturan.profil', compact('admin'));
    }

    /**
     * 🔹 Update data profil + foto + password.
     */
    public function update(Request $request)
    {
        $admin = auth()->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:admins,username,' . $admin->id,
            'date_in' => 'nullable|date',
            'position' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',

            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',

            'date_in.date' => 'Format tanggal tidak valid.',

            'position.max' => 'Jabatan maksimal 50 karakter.',

            'phone.max' => 'Nomor telepon maksimal 20 karakter.',

            'photo.image' => 'File foto harus berupa gambar.',
            'photo.mimes' => 'Format foto harus jpeg, png, jpg, atau webp.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',

            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Update data dasar
        $admin->fill([
            'name' => $request->name,
            'username' => $request->username,
            'date_in' => $request->date_in,
            'position' => $request->position,
            'phone' => $request->phone,
        ]);

        // Upload foto baru (jika ada)
        if ($request->hasFile('photo')) {

            // Hapus foto lama jika ada dan file-nya benar-benar ada
            if (!empty($admin->img) && \Storage::disk('public')->exists('admins/' . $admin->img)) {
                \Storage::disk('public')->delete('admins/' . $admin->img);
            }

            // Simpan foto baru
            $path = $request->file('photo')->store('admins', 'public');
            $admin->img = basename($path);
        }

        // Ganti password jika diisi
        if ($request->filled('current_password') && $request->filled('new_password')) {

            if (!Hash::check($request->current_password, $admin->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
            }

            $admin->password = Hash::make($request->new_password);
        }

        $admin->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }



    /**
     * 🔹 Nonaktifkan akun admin.
     */
    public function destroy()
    {
        $admin = Auth::user();
        $admin->role = 0;
        $admin->save();

        Auth::logout();

        return redirect()->route('login')->with('success', 'Akun Anda telah dinonaktifkan.');
    }
}
