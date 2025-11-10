<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Competence;
use App\Models\Lesson;

class CompetenceController extends Controller
{
    /**
     * Tampilkan daftar KD untuk pelajaran.
     */
    public function index($lesson_id)
    {
        $lesson = Lesson::with('mapel')->findOrFail($lesson_id);
        $competences = Competence::where('lesson_id', $lesson_id)->orderBy('id')->get();

        return view('admin.pelajaran.kd', compact('lesson', 'competences'));
    }

    /**
     * Simpan KD baru.
     */
    public function store(Request $request, $lesson_id)
    {
        try {
            // Validasi manual (agar balikan JSON rapi)
            $validator = \Validator::make(
                $request->all(),
                [
                    'description' => 'required|string',
                ],
                [
                    'description.required' => 'Deskripsi KD wajib diisi.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $lesson = Lesson::find($lesson_id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelajaran tidak ditemukan.',
                ], 404);
            }

            // ID manual (karena tabel tidak auto increment)
            $lastId = Competence::max('id');
            $newId = $lastId ? $lastId + 1 : 1;

            // Ambil KD terakhir berdasarkan pelajaran
            $lastKD = Competence::where('lesson_id', $lesson_id)
                ->orderByDesc('id')
                ->first();

            // Generate nomor KD berikut
            $nextNumber = $lastKD
                ? intval(substr($lastKD->point, 3)) + 1
                : 1;

            $point = 'KD-' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

            $competence = Competence::create([
                'id' => $newId,
                'lesson_id' => $lesson_id,
                'mapel_id' => $lesson->mapel_id,
                'point' => $point,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kompetensi berhasil ditambahkan.',
                'data' => $competence,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kompetensi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil data KD untuk edit.
     */
    public function edit($lesson_id, $id)
    {
        $competence = Competence::find($id);

        if (!$competence) {
            return response()->json([
                'success' => false,
                'message' => 'Kompetensi tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $competence,
        ]);
    }

    /**
     * Update data KD.
     */
    public function update(Request $request, $lesson_id, $id)
    {
        $competence = Competence::where('id', $id)
            ->where('lesson_id', $lesson_id)
            ->first();

        if (!$competence) {
            return response()->json([
                'success' => false,
                'message' => 'Kompetensi tidak ditemukan atau tidak sesuai pelajaran.',
            ], 404);
        }

        // 🔹 Tambahkan validasi untuk "point" juga
        $validator = \Validator::make(
            $request->all(),
            [
                'point' => 'required|string|max:20',
                'description' => 'required|string',
            ],
            [
                'point.required' => 'Kode KD wajib diisi.',
                'description.required' => 'Deskripsi KD wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // 🔹 Update kedua field
            $competence->update([
                'point' => $request->point,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kompetensi berhasil diperbarui.',
                'data' => $competence,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kompetensi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus KD.
     */
    public function destroy($lesson_id, $id)
    {
        $competence = Competence::find($id);

        if (!$competence) {
            return response()->json([
                'success' => false,
                'message' => 'Kompetensi tidak ditemukan.',
            ], 404);
        }

        try {
            $competence->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kompetensi berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kompetensi tidak dapat dihapus karena masih terhubung dengan data lain.',
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kompetensi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
