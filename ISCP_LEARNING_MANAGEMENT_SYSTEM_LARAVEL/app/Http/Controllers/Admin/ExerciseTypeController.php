<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\ExerciseType;

class ExerciseTypeController extends Controller
{
    public const ALLOWED_ROLES = [1, 2, 4];
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tampilkan daftar tipe soal.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExerciseType::orderBy('id', 'asc')->get();
            return response()->json(['data' => $data]);
        }

        $data = ExerciseType::orderBy('id', 'asc')->get();

        return view('admin.pra-soal.tipe', compact('data'));
    }

    /**
     * Simpan data baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validator = \Validator::make(
                $request->all(),
                [
                    'kode' => 'required|string|max:10|unique:exercise_types,kode',
                    'name' => 'required|string|max:50|unique:exercise_types,name',
                ],
                [
                    'kode.required' => 'Kode wajib diisi.',
                    'kode.unique' => 'Kode sudah digunakan, silakan pilih kode lain.',
                    'name.required' => 'Nama tipe soal wajib diisi.',
                    'name.unique' => 'Nama tipe soal sudah digunakan, silakan pilih nama lain.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }



            $type = ExerciseType::create([

                'kode' => $request->kode,
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipe soal berhasil ditambahkan.',
                'data' => $type,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan tipe soal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil data tipe soal berdasarkan ID.
     */
    public function edit($id)
    {
        $type = ExerciseType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe soal tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $type,
        ]);
    }

    /**
     * Update data tipe soal berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $type = ExerciseType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe soal tidak ditemukan.',
            ], 404);
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'kode' => 'required|string|max:10|unique:exercise_types,kode,' . $id,
                'name' => 'required|string|max:50|unique:exercise_types,name,' . $id,
            ],
            [
                'kode.required' => 'Kode wajib diisi.',
                'kode.unique' => 'Kode sudah digunakan oleh tipe lain.',
                'name.required' => 'Nama wajib diisi.',
                'name.unique' => 'Nama sudah digunakan oleh tipe lain.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $type->update($request->only(['kode', 'name']));

            return response()->json([
                'success' => true,
                'message' => 'Tipe soal berhasil diperbarui.',
                'data' => $type,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tipe soal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus data tipe soal.
     */
    public function destroy($id)
    {
        $type = ExerciseType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe soal tidak ditemukan.',
            ], 404);
        }

        $relatedData = [];

        if (\App\Models\Exercise::where('exercise_type_id', $id)->exists()) {
            $relatedData[] = 'soal';
        }

        if (\App\Models\ExerciseItem::where('exercise_type_id', $id)->exists()) {
            $relatedData[] = 'item soal';
        }

        if (!empty($relatedData)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe soal tidak dapat dihapus karena masih digunakan di: ' . implode(', ', $relatedData),
            ], 409);
        }


        try {
            $type->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipe soal berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tipe soal: ' . $e->getMessage(),
            ], 500);
        }
    }

}
