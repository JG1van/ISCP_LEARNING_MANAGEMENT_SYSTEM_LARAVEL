<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\ExerciseType;

class ExerciseTypeController extends Controller
{
    /**
     * Middleware untuk autentikasi admin.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tampilkan daftar tipe latihan.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExerciseType::orderBy('id', 'asc')->get();
            return response()->json(['data' => $data]);
        }

        $data = ExerciseType::orderBy('id', 'asc')->get();

        return view('admin.pra_latihan.tipe', compact('data'));
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
                    'name.required' => 'Nama tipe latihan wajib diisi.',
                    'name.unique' => 'Nama tipe latihan sudah digunakan, silakan pilih nama lain.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            // Buat ID manual
            $lastId = ExerciseType::max('id');
            $newId = $lastId ? $lastId + 1 : 1;

            $type = ExerciseType::create([
                'id' => $newId,
                'kode' => $request->kode,
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipe latihan berhasil ditambahkan.',
                'data' => $type,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan tipe latihan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil data tipe latihan berdasarkan ID.
     */
    public function edit($id)
    {
        $type = ExerciseType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe latihan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $type,
        ]);
    }

    /**
     * Update data tipe latihan berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $type = ExerciseType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe latihan tidak ditemukan.',
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
                'message' => 'Tipe latihan berhasil diperbarui.',
                'data' => $type,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tipe latihan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus data tipe latihan.
     */
    public function destroy($id)
    {
        $type = ExerciseType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe latihan tidak ditemukan.',
            ], 404);
        }

        try {
            $type->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipe latihan berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe latihan tidak dapat dihapus karena masih terhubung dengan data lain.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tipe latihan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
