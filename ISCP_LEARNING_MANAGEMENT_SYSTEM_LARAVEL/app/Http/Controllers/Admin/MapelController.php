<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mapel;
use App\Models\Lesson;
use Illuminate\Database\QueryException;

class MapelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tampilkan halaman utama manajemen mapel
     * Jika AJAX: kirim data JSON
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $mapels = Mapel::orderBy('id', 'asc')->get();
            return response()->json(['data' => $mapels]);
        }

        $data = Mapel::orderBy('id', 'asc')->get();
        return view('admin.mapel.index', compact('data'));
    }

    /**
     * Tambah mapel baru via AJAX
     */
    public function store(Request $request)
    {
        // Validasi manual agar bisa kembalikan JSON error
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:20|unique:mapels,name',
            ],
            [
                'name.required' => 'Nama Kategori Pelajaran wajib diisi.',
                'name.unique' => 'Nama Kategori Pelajaran sudah digunakan, silakan pilih nama lain.',
                'name.max' => 'Nama Kategori Pelajaran maksimal 20 karakter.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Buat ID manual (karena tabel tidak auto_increment)
            $lastId = Mapel::max('id');
            $newId = $lastId ? $lastId + 1 : 1;

            $mapel = Mapel::create([
                'id' => $newId,
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori Pelajaran berhasil ditambahkan.',
                'data' => $mapel
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan Kategori Pelajaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil data mapel untuk diedit
     */
    public function edit($id)
    {
        $mapel = Mapel::find($id);

        if (!$mapel) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Pelajaran tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $mapel,
        ]);
    }

    /**
     * Update data mapel via AJAX
     */
    public function update(Request $request, $id)
    {
        $mapel = Mapel::find($id);

        if (!$mapel) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Pelajaran tidak ditemukan.',
            ], 404);
        }

        // Validasi manual seperti store()
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:20|unique:mapels,name,' . $id,
            ],
            [
                'name.required' => 'Nama Kategori Pelajaran wajib diisi.',
                'name.unique' => 'Nama Kategori Pelajaran sudah digunakan oleh Kategori Pelajaran lain.',
                'name.max' => 'Nama Kategori Pelajaran maksimal 20 karakter.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $mapel->update(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori Pelajaran berhasil diperbarui.',
                'data' => $mapel
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Kategori Pelajaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus mapel via AJAX
     */
    public function destroy($id)
    {
        $mapel = Mapel::find($id);

        if (!$mapel) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Pelajaran tidak ditemukan.',
            ], 404);
        }

        // Cek apakah mapel dipakai di tabel pelajaran
        $lessons = Lesson::where('mapel_id', $id)->pluck('name');

        if ($lessons->count() > 0) {
            $lessonList = $lessons->implode(', ');
            return response()->json([
                'success' => false,
                'message' => 'Kategori Pelajaran tidak dapat dihapus karena masih digunakan oleh pelajaran: ' . $lessonList,
            ], 409);
        }

        try {
            $mapel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori Pelajaran berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Pelajaran gagal dihapus: ' . $e->getMessage(),
            ], 500);
        }
    }
}
