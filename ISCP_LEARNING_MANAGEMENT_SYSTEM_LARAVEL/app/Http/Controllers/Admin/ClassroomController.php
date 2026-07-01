<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use App\Models\Classroom;
use App\Models\Serial;
use App\Models\User;
use App\Models\Student;
use App\Models\Report;
use App\Models\Task;
use Carbon\Carbon;

class ClassroomController extends Controller
{
    public const ALLOWED_ROLES = [1, 2, 3];

    public function __construct()
    {

        $this->middleware(['auth']);
    }

    /**
     * Halaman utama daftar kelas
     */
    public function index()
    {
        // Ambil semua data kelas beserta relasi penting
        $data = Classroom::with(['serial.user', 'students', 'user'])
            ->withCount('students')
            ->orderBy('id', 'asc')
            ->get();

        // Ambil semua serial dan pengguna (guru)
        $serials = Serial::with('classrooms')->orderBy('id', 'asc')->get();
        $users = User::orderBy('id', 'asc')->get();

        // Array untuk menyimpan serial yang melebihi batas paket
        $warningSerials = [];
        $serialBermasalah = [];

        foreach ($serials as $serial) {
            $kelasCount = $serial->classrooms->count();
            $paketCount = (int) ($serial->paket ?? 1); // pastikan default 1 jika null

            if ($kelasCount > $paketCount) {
                $warningSerials[] = [
                    'kode_serial' => $serial->serial ?? '-',
                    'paket' => $paketCount,
                    'kelas' => $kelasCount,
                    'username' => $serial->user->username ?? 'Tidak Ada Pengguna',
                    'daftar_kelas' => $serial->classrooms->pluck('name')->toArray(),
                ];


                // Simpan kode serial bermasalah (opsional untuk keperluan JS / view)
                $serialBermasalah[] = $serial->serial;
            }
        }

        // Kirim semua data ke view
        return view('admin.kelas.index', compact(
            'data',
            'serials',
            'users',
            'warningSerials',
            'serialBermasalah'
        ));
    }

    /**
     * Simpan data kelas baru
     */
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'serial_id' => 'required|exists:serials,id',
                'name' => 'required|string|max:100|unique:classrooms,name',
                'grade' => 'required|string|max:10',
            ],
            [
                'serial_id.required' => 'Serial wajib dipilih.',
                'serial_id.exists' => 'Serial tidak ditemukan.',
                'name.required' => 'Nama kelas wajib diisi.',
                'name.unique' => 'Nama kelas sudah digunakan.',
                'grade.required' => 'Kelas wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $serial = Serial::find($request->serial_id);
            if (!$serial) {
                return response()->json(['success' => false, 'message' => 'Serial tidak ditemukan.'], 404);
            }

            // 🚫 Cek batas paket
            $usedClassCount = Classroom::where('serial_id', $serial->id)->count();
            if ($usedClassCount >= $serial->paket) {
                return response()->json([
                    'success' => false,
                    'message' => "Batas pembuatan kelas tercapai! Paket hanya mendukung {$serial->paket} kelas.",
                ], 422);
            }


            //   Generate kode unik
            $code = $this->generateCode();

            //   Simpan kelas baru
            $classroom = Classroom::create([
                'serial_id' => $serial->id,
                'name' => $request->name,
                'grade' => $request->grade,
                'code' => $code,
            ]);

            //   Update expired_at di serial (jika ada durasi aktif)
            if (!empty($serial->active)) {
                $serial->expired_at = now()->addMonths($serial->active);
                $serial->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil ditambahkan.',
                'data' => $classroom,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kelas: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Ambil data kelas untuk edit
     */
    public function edit($id)
    {
        // Muat relasi serial dan user-nya
        $classroom = Classroom::with(['serial.user'])->find($id);

        if (!$classroom) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $classroom
        ]);
    }


    /**
     * Update data kelas
     */
    public function update(Request $request, $id)
    {
        $classroom = Classroom::find($id);
        if (!$classroom) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan.'], 404);
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'serial_id' => 'required|exists:serials,id',
                'name' => 'required|string|max:100|unique:classrooms,name,' . $id,
                'grade' => 'required|string|max:10',
            ],
            [
                'serial_id.required' => 'Serial wajib diisi.',
                'serial_id.exists' => 'Serial yang dipilih tidak valid.',
                'name.required' => 'Nama kelas wajib diisi.',
                'name.string' => 'Nama kelas harus berupa teks.',
                'name.max' => 'Nama kelas maksimal 100 karakter.',
                'name.unique' => 'Nama kelas sudah digunakan oleh kelas lain.',
                'grade.required' => 'Tingkat kelas wajib diisi.',
                'grade.string' => 'Tingkat kelas harus berupa teks.',
                'grade.max' => 'Tingkat kelas maksimal 10 karakter.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $serial = Serial::find($request->serial_id);

            // Cek batas paket
            $usedCount = Classroom::where('serial_id', $serial->id)
                ->where('id', '!=', $id)
                ->count();

            if ($usedCount >= $serial->paket) {
                return response()->json([
                    'success' => false,
                    'message' => "Serial sudah mencapai batas maksimum ({$serial->paket}).",
                ], 403);
            }

            // Update kelas dan expired_at
            $expiredAt = now()->addMonths($serial->active);

            $classroom->update([
                'serial_id' => $serial->id,
                'name' => $request->name,
                'grade' => $request->grade,
                'expired_at' => $expiredAt,
            ]);

            $serial->update(['expired_at' => $expiredAt]);

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil diperbarui.',
                'data' => $classroom,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui kelas: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hapus kelas
     */
    public function destroy($id)
    {
        $classroom = Classroom::find($id);
        if (!$classroom) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan.'], 404);
        }

        $relatedData = [];
        if (\App\Models\Student::where('classroom_id', $id)->exists())
            $relatedData[] = 'siswa';
        // if (\App\Models\Report::where('classroom_id', $id)->exists())
        //     $relatedData[] = 'laporan';
        // if (\App\Models\Task::where('classroom_id', $id)->exists())
        //     $relatedData[] = 'tugas';

        if (!empty($relatedData)) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak dapat dihapus karena masih terkait dengan: ' . implode(', ', $relatedData),
            ], 409);
        }

        try {
            $classroom->delete();
            return response()->json(['success' => true, 'message' => 'Kelas berhasil dihapus.']);
        } catch (\Exception $e) {
            // Kembalikan pesan error spesifik agar JS bisa memunculkan notif
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kelas: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Generate kode unik
     */
    private function generateCode()
    {
        do {
            $code = Str::random(24);
        } while (Classroom::where('code', $code)->exists());
        return $code;
    }
}
