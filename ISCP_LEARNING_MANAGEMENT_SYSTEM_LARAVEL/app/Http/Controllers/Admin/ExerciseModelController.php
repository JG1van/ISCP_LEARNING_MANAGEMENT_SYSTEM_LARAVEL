<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\ExerciseModel;

class ExerciseModelController extends Controller
{
    /**
     * Middleware untuk autentikasi admin.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tampilkan daftar model latihan.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExerciseModel::orderBy('id', 'asc')->get();
            return response()->json(['data' => $data]);
        }

        $data = ExerciseModel::orderBy('id', 'asc')->get();

        return view('admin.pra_latihan.model', compact('data'));
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
                    'name.required' => 'Nama model latihan wajib diisi.',
                    'name.unique' => 'Nama model latihan sudah digunakan.',
                    'name.max' => 'Nama model latihan maksimal 20 karakter.',
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
                'message' => 'Model latihan berhasil ditambahkan.',
                'data' => $model,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan model latihan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil data model latihan berdasarkan ID.
     */
    public function edit($id)
    {
        $model = ExerciseModel::find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model latihan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    /**
     * Update data model latihan.
     */
    public function update(Request $request, $id)
    {
        $model = ExerciseModel::find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model latihan tidak ditemukan.',
            ], 404);
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:20|unique:exercise_models,name,' . $id,
            ],
            [
                'name.required' => 'Nama model latihan wajib diisi.',
                'name.unique' => 'Nama model latihan sudah digunakan oleh model lain.',
                'name.max' => 'Nama model latihan maksimal 20 karakter.',
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
                'message' => 'Model latihan berhasil diperbarui.',
                'data' => $model,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui model latihan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus model latihan.
     */
    public function destroy($id)
    {
        $model = ExerciseModel::find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model latihan tidak ditemukan.',
            ], 404);
        }

        // Cek apakah model latihan masih digunakan
        $relatedData = [];

        if (\App\Models\ExerciseItem::where('exercise_model_id', $id)->exists()) {
            $relatedData[] = 'soal';
        }

        // Jika masih digunakan, tolak penghapusan
        if (!empty($relatedData)) {
            $list = implode(', ', $relatedData);
            return response()->json([
                'success' => false,
                'message' => "Model latihan tidak dapat dihapus karena masih terhubung dengan data: {$list}.",
            ], 409);
        }

        try {
            $model->delete();

            return response()->json([
                'success' => true,
                'message' => 'Model latihan berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Model latihan tidak dapat dihapus karena masih terhubung dengan data lain.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus model latihan: ' . $e->getMessage(),
            ], 500);
        }
    }

}
