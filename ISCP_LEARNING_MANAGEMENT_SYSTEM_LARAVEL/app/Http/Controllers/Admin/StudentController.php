<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Serial;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     *    Tampilkan daftar siswa
     */
    public function index(Request $request)
    {
        // Ambil semua siswa
        if ($request->ajax()) {
            $students = \App\Models\Student::orderBy('id', 'asc')->get();
            return response()->json(['data' => $students]);
        }

        // Ambil semua siswa dan kelas
        $students = \App\Models\Student::orderBy('id', 'asc')->get();
        $classrooms = \App\Models\Classroom::with(['serial.user'])->orderBy('name', 'asc')->get();

        return view('admin.siswa.index', compact('students', 'classrooms'));
    }
    public function create(Request $request)
    {
        $classroomId = $request->query('classroom_id');

        if ($classroomId) {
            // Ambil classroom terpilih beserta relasi serial.user
            $classroom = Classroom::with(['serial.user'])->find($classroomId);

            if (!$classroom) {
                // Kalau kelas tidak ditemukan, redirect kembali ke index (ubah route sesuai)
                return redirect()->route('siswa.index')->with('error', 'Kelas tidak ditemukan.');
            }

            // Ambil siswa pada classroom ini (urutkan sesuai kebutuhan)
            $students = Student::where('classroom_id', $classroom->id)
                ->orderBy('id', 'asc')
                ->get();

            return view('admin.siswa.create', compact('classroom', 'students'));
        }

        // Kalau belum memilih kelas: kirim daftar classroom untuk dipilih + students kosong
        $classrooms = Classroom::with(['serial.user'])->orderBy('id', 'asc')->get();
        $students = collect(); // collection kosong supaya blade aman
        return view('admin.siswa.create', compact('classrooms', 'students'));
    }


    /**
     *    Simpan data siswa baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serial_id' => 'required|integer',
            'user_id' => 'required|integer',
            'classroom_id' => 'required|integer',
            'name' => 'required|string|max:200',
            'username' => 'required|string|max:100|unique:students,username',
            'nis' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
        ], [
            'serial_id.required' => 'Serial wajib diisi.',
            'user_id.required' => 'User wajib diisi.',
            'classroom_id.required' => 'Kelas wajib diisi.',
            'name.required' => 'Nama siswa wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {

            //   Password default
            $defaultPassword = 'Siswa1234';

            $student = Student::create([
                'serial_id' => $request->serial_id,
                'user_id' => $request->user_id,
                'classroom_id' => $request->classroom_id,
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($defaultPassword),
                'password_text' => $defaultPassword,
                'nis' => $request->nis,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil ditambahkan.',
                'data' => $student
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan siswa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     *    Ambil data siswa berdasarkan ID
     */
    public function edit($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    /**
     *    Update data siswa
     */
    public function update(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);

            $request->validate([
                'serial_id' => 'required|integer',
                'user_id' => 'required|integer',
                'classroom_id' => 'required|integer',
                'name' => 'required|string|max:200',
                'username' => 'required|string|max:100|unique:students,username,' . $id,
                'nis' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'phone' => 'nullable|string|max:20',
            ]);

            $student->update([
                'serial_id' => $request->serial_id,
                'user_id' => $request->user_id,
                'classroom_id' => $request->classroom_id,
                'name' => $request->name,
                'username' => $request->username,
                'nis' => $request->nis,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diperbarui.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui siswa: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     *    Hapus data siswa
     */
    public function destroy($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan.'
            ], 404);
        }

        // Cek apakah siswa masih digunakan di tabel lain
        $relatedData = [];

        if (\App\Models\Report::where('student_id', $id)->exists()) {
            $relatedData[] = 'laporan';
        }

        if (\App\Models\Task::where('student_id', $id)->exists()) {
            $relatedData[] = 'tugas';
        }

        if (\App\Models\ExercisePoint::where('student_id', $id)->exists()) {
            $relatedData[] = 'nilai soal';
        }

        if (!empty($relatedData)) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak dapat dihapus karena masih terkait dengan: ' . implode(', ', $relatedData)
            ], 409);
        }

        try {
            $student->delete();
            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus siswa: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     *    Reset Password Siswa
     */
    public function resetPassword($id)
    {
        try {
            $student = Student::findOrFail($id);

            // Default password = username
            $newPassword = 'Siswa1234';

            $student->update([
                'password' => Hash::make($newPassword),
                'password_text' => $newPassword,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset ke: ' . $newPassword,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal reset password: ' . $e->getMessage(),
            ]);
        }
    }


}

