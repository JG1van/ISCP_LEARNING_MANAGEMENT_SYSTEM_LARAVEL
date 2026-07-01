<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\ExerciseModel;

class ExerciseModelController extends Controller
{
    public const ALLOWED_ROLES = [1, 2, 4];
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tampilkan daftar model soal.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExerciseModel::orderBy('id', 'asc')->get();
            return response()->json(['data' => $data]);
        }

        $data = ExerciseModel::orderBy('id', 'asc')->get();

        return view('admin.pra-soal.model', compact('data'));
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
                    'name' => 'required|string|max:20|unique:exercise_models,name',
                ],
                [
                    'name.required' => 'Nama model soal wajib diisi.',
                    'name.unique' => 'Nama model soal sudah digunakan.',
                    'name.max' => 'Nama model soal maksimal 20 karakter.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }



            $model = ExerciseModel::create([

                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Model soal berhasil ditambahkan.',
                'data' => $model,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan model soal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil data model soal berdasarkan ID.
     */
    public function edit($id)
    {
        $model = ExerciseModel::find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model soal tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    /**
     * Update data model soal.
     */
    public function update(Request $request, $id)
    {
        $model = ExerciseModel::find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model soal tidak ditemukan.',
            ], 404);
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:20|unique:exercise_models,name,' . $id,
            ],
            [
                'name.required' => 'Nama model soal wajib diisi.',
                'name.unique' => 'Nama model soal sudah digunakan oleh model lain.',
                'name.max' => 'Nama model soal maksimal 20 karakter.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $model->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Model soal berhasil diperbarui.',
                'data' => $model,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui model soal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus model soal.
     */
    public function destroy($id)
    {
        $model = ExerciseModel::find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model soal tidak ditemukan.',
            ], 404);
        }

        // Cek apakah model soal masih digunakan
        $relatedData = [];

        if (\App\Models\ExerciseItem::where('exercise_model_id', $id)->exists()) {
            $relatedData[] = 'soal';
        }

        // Jika masih digunakan, tolak penghapusan
        if (!empty($relatedData)) {
            $list = implode(', ', $relatedData);
            return response()->json([
                'success' => false,
                'message' => "Model soal tidak dapat dihapus karena masih terhubung dengan data: {$list}.",
            ], 409);
        }

        try {
            $model->delete();

            return response()->json([
                'success' => true,
                'message' => 'Model soal berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Model soal tidak dapat dihapus karena masih terhubung dengan data lain.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus model soal: ' . $e->getMessage(),
            ], 500);
        }
    }

}
