<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Admin;
use App\Models\User;        // Guru
use App\Models\Student;     // Siswa

class LoginAllController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');   // view login umum
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $username = $request->username;
        $password = $request->password;

        // =======================================================
        // 1. LOGIN ADMIN (GUARD WEB)
        // =======================================================
        if (Auth::guard('web')->attempt(['username' => $username, 'password' => $password])) {

            $request->session()->regenerate();

            $admin = Auth::guard('web')->user();
            $admin->login_at = now();
            $admin->save();

            $roles = [
                1 => 'Super-Admin',
                2 => 'Admin',
                3 => 'Operasional',
                4 => 'Konten-Pembelajaran',
                5 => 'Layanan-Pengguna',
            ];
            $roleName = $roles[$admin->role] ?? 'Admin';

            // 🔔 NOTIFIKASI ADMIN
            $message = "
                <b>Berhasil login!</b><br>
                Selamat datang, <b>{$admin->username}</b><br>
                Selaku <b>{$roleName}</b>.<br>
                Selamat bekerja.
            ";

            return redirect()->route('admin.dashboard')
                ->with('success_html', $message);
        }

        // =======================================================
        // 2. LOGIN GURU (USERS TABLE)
        // =======================================================
        $guru = User::where('username', $username)->first();

        if ($guru && Hash::check($password, $guru->password)) {

            Auth::login($guru);
            session(['role' => 'guru']);

            // 🔔 NOTIFIKASI GURU
            $message = "
                <b>Berhasil login!</b><br>
                Selamat datang, <b>{$guru->username}</b><br>
                <b>Siap mengajar!</b>
            ";

            return redirect()->route('guru.dashboard')
                ->with('success_html', $message);
        }

        // =======================================================
        // 3. LOGIN SISWA (STUDENTS TABLE)
        // =======================================================
        $student = Student::where('username', $username)->first();

        if ($student && Hash::check($password, $student->password)) {

            Auth::login($student);
            session(['role' => 'siswa']);

            // 🔔 NOTIFIKASI SISWA
            $message = "
                <b>Berhasil login!</b><br>
                Selamat datang, <b>{$student->username}</b><br>
                <b>Siap belajar!</b>
            ";

            return redirect()->route('student.dashboard')
                ->with('success_html', $message);
        }

        // =======================================================
        // LOGIN GAGAL
        // =======================================================
        return back()->withErrors([
            'username' => 'Username atau password salah.'
        ]);
    }

    public function loginAjax(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'room_code' => 'nullable|string'
        ]);

        $username = $request->username;
        $password = $request->password;
        $roomCode = $request->room_code;

        // =============== LOGIN GURU ===============
        $guru = User::where('username', $username)->first();

        if ($guru && Hash::check($password, $guru->password)) {

            Auth::login($guru);
            session(['role' => 'guru']);

            if ($roomCode) {
                \App\Models\CSRoom::where('room_code', $roomCode)
                    ->update([
                        'user_id' => $guru->id,
                        'student_id' => null,
                        'admin_id' => null
                    ]);
            }

            return response()->json([
                'status' => 'success',
                'role' => 'guru',
                'username' => $guru->username
            ], 200);
        }

        // =============== LOGIN SISWA ===============
        $student = Student::where('username', $username)->first();

        if ($student && Hash::check($password, $student->password)) {

            Auth::login($student);
            session(['role' => 'siswa']);

            if ($roomCode) {
                \App\Models\CSRoom::where('room_code', $roomCode)
                    ->update([
                        'student_id' => $student->id,
                        'user_id' => null,
                        'admin_id' => null
                    ]);
            }

            return response()->json([
                'status' => 'success',
                'role' => 'siswa',
                'username' => $student->username
            ], 200);
        }

        // =============== LOGIN GAGAL ===============
        return response()->json([
            'status' => 'error',
            'message' => 'Username atau password salah.'
        ], 401);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Berhasil logout.');
    }
}
