<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Lesson;
use App\Models\Mapel;
use App\Models\Theme;

class LessonController extends Controller
{
    /**
     * Middleware untuk autentikasi admin.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tampilkan daftar lesson.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Lesson::with('mapel')->orderBy('id', 'asc')->get();
            return response()->json(['data' => $data]);
        }

        $data = Lesson::with('mapel')->orderBy('id', 'asc')->get();
        $mapels = Mapel::orderBy('id', 'asc')->get();

        return view('admin.pelajaran.index', compact('data', 'mapels'));
    }

    /**
     * Simpan data baru ke database.
     */
    public function store(Request $request)
    {
        try {
            // validasi manual supaya bisa kirim JSON saat gagal
            $validator = \Validator::make(
                $request->all(),
                [
                    'mapel_id' => 'required|exists:mapels,id',
                    'name' => 'required|string|max:50|unique:lessons,name',
                    'grade' => 'required|string|max:10',
                    'semester' => 'required|integer|min:1|max:2',
                    'category' => 'required|integer|min:1|max:3',
                ],
                [
                    'mapel_id.required' => 'Mata pelajaran wajib dipilih.',
                    'mapel_id.exists' => 'Mata pelajaran tidak ditemukan.',
                    'name.required' => 'Nama pelajaran wajib diisi.',
                    'name.unique' => 'Nama pelajaran sudah digunakan, silakan pilih nama lain.',
                    'grade.required' => 'Kelas wajib diisi.',
                    'semester.required' => 'Semester wajib diisi.',
                    'category.required' => 'Kategori wajib diisi.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(), // tampilkan error validasi pertama
                ], 422);
            }

            // buat id manual karena tabel tidak auto_increment
            $lastId = Lesson::max('id');
            $newId = $lastId ? $lastId + 1 : 1;

            $lesson = Lesson::create([
                'id' => $newId,
                'mapel_id' => $request->mapel_id,
                'name' => $request->name,
                'grade' => $request->grade,
                'semester' => $request->semester,
                'category' => $request->category,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pelajaran berhasil ditambahkan.',
                'data' => $lesson,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pelajaran: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Ambil data lesson berdasarkan id.
     */
    public function edit($id)
    {
        $lesson = Lesson::with('mapel')->find($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Pelajaran tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $lesson,
        ]);
    }

    /**
     * Update data lesson berdasarkan id.
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Pelajaran tidak ditemukan.',
            ], 404);
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'mapel_id' => 'required|exists:mapels,id',
                'name' => 'required|string|max:50|unique:lessons,name,' . $id,
                'grade' => 'required|string|max:10',
                'semester' => 'required|integer|min:1|max:2',
                'category' => 'required|integer|min:1|max:3',
            ],
            [
                'mapel_id.required' => 'Mata pelajaran wajib dipilih.',
                'mapel_id.exists' => 'Mata pelajaran tidak ditemukan.',
                'name.required' => 'Nama pelajaran wajib diisi.',
                'name.unique' => 'Nama pelajaran sudah digunakan oleh pelajaran lain.',
                'grade.required' => 'Kelas wajib diisi.',
                'semester.required' => 'Semester wajib diisi.',
                'category.required' => 'Kategori wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $lesson->update($request->only([
                'mapel_id',
                'name',
                'grade',
                'semester',
                'category'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Pelajaran berhasil diperbarui.',
                'data' => $lesson,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Pelajaran: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Hapus data lesson.
     */
    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Pelajaran tidak ditemukan.',
            ], 404);
        }

        // // Cek relasi ke Theme
        // $themes = Theme::where('lesson_id', $id)->pluck('name');
        // if ($themes->count() > 0) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Pelajaran tidak dapat dihapus karena masih terhubung dengan tema: ' . $themes->implode(', '),
        //     ], 409);
        // }


        try {
            $lesson->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pelajaran berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelajaran tidak dapat dihapus karena masih terhubung dengan data lain.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Pelajaran: ' . $e->getMessage(),
            ], 500);
        }
    }
}
