<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\ExerciseType;
use App\Models\Serial;

class ExerciseController extends Controller
{
    /**
     * Middleware untuk autentikasi admin.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tampilkan daftar latihan berdasarkan pelajaran.
     */
    public function index(Request $request, $lesson_id)
    {
        $lesson = Lesson::find($lesson_id);
        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Pelajaran tidak ditemukan.',
            ], 404);
        }

        if ($request->ajax()) {
            $data = Exercise::with(['exercise_type', 'serial', 'lesson'])
                ->where('lesson_id', $lesson_id)
                ->orderBy('id', 'asc')
                ->get();

            return response()->json(['success' => true, 'data' => $data]);
        }

        $data = Exercise::with(['exercise_type', 'serial'])
            ->where('lesson_id', $lesson_id)
            ->orderBy('id', 'asc')
            ->get();
        $types = ExerciseType::orderBy('id', 'asc')->get();
        $serials = Serial::orderBy('id', 'asc')->get();

        return view('admin.pelajaran.latihan_soal', compact('lesson', 'data', 'types', 'serials'));
    }

    /**
     * Simpan latihan baru.
     */
    public function store(Request $request, $lesson_id)
    {
        try {
            $validator = \Validator::make(
                $request->all(),
                [
                    'exercise_type_id' => 'required|exists:exercise_types,id',
                    'title' => 'required|string|max:200|unique:exercises,title',
                    'serial_id' => 'nullable|exists:serials,id',
                ],
                [
                    'exercise_type_id.required' => 'Tipe latihan wajib dipilih.',
                    'exercise_type_id.exists' => 'Tipe latihan tidak ditemukan.',
                    'title.required' => 'Judul latihan wajib diisi.',
                    'title.unique' => 'Judul latihan sudah digunakan, silakan pilih judul lain.',
                    'title.max' => 'Judul latihan maksimal 200 karakter.',
                    'serial_id.exists' => 'Serial tidak valid.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            // Buat ID manual jika tabel tidak auto_increment
            $lastId = Exercise::max('id');
            $newId = $lastId ? $lastId + 1 : 1;

            $exercise = Exercise::create([
                'id' => $newId,
                'lesson_id' => $lesson_id,
                'exercise_type_id' => $request->exercise_type_id,
                'serial_id' => $request->serial_id,
                'title' => $request->title,
                'is_admin' => 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Latihan berhasil ditambahkan.',
                'data' => $exercise,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan latihan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil data latihan berdasarkan ID.
     */
    public function edit($lesson_id, $id)
    {
        $exercise = Exercise::with(['exercise_type', 'serial'])->where('lesson_id', $lesson_id)->find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Latihan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $exercise,
        ]);
    }

    /**
     * Update data latihan.
     */
    public function update(Request $request, $lesson_id, $id)
    {
        $exercise = Exercise::where('lesson_id', $lesson_id)->find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Latihan tidak ditemukan.',
            ], 404);
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'exercise_type_id' => 'required|exists:exercise_types,id',
                'title' => 'required|string|max:200|unique:exercises,title,' . $id,
                'serial_id' => 'nullable|exists:serials,id',
            ],
            [
                'exercise_type_id.required' => 'Tipe latihan wajib dipilih.',
                'exercise_type_id.exists' => 'Tipe latihan tidak ditemukan.',
                'title.required' => 'Judul latihan wajib diisi.',
                'title.unique' => 'Judul latihan sudah digunakan oleh latihan lain.',
                'title.max' => 'Judul latihan maksimal 200 karakter.',
                'serial_id.exists' => 'Serial tidak valid.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $exercise->update($request->only([
                'exercise_type_id',
                'title',
                'serial_id',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Latihan berhasil diperbarui.',
                'data' => $exercise,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui latihan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus latihan.
     */
    public function destroy($lesson_id, $id)
    {
        $exercise = Exercise::where('lesson_id', $lesson_id)->find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Latihan tidak ditemukan.',
            ], 404);
        }

        try {
            $exercise->delete();

            return response()->json([
                'success' => true,
                'message' => 'Latihan berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Latihan tidak dapat dihapus karena masih terhubung dengan data lain.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus latihan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
