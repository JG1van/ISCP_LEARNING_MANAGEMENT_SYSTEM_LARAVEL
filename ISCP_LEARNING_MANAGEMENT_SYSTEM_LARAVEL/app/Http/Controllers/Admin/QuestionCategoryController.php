<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuestionCategory;
use App\Models\CSRoom;
use App\Models\CSLog;
use Illuminate\Support\Facades\Storage;

class QuestionCategoryController extends Controller
{
    public const ALLOWED_ROLES = [1, 2, 5];

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $data = QuestionCategory::orderBy('id', 'asc')->get();

        if ($request->ajax()) {
            return response()->json(['data' => $data]);
        }

        return view('admin.kategori-pertanyaan.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:question_categories,name',
            'level' => 'required|in:Umum,Siswa,Guru',
            'solution_text' => 'nullable|string',
            'guide_file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:2048',
            'guide_video' => 'nullable|string|max:2000',
            'category_status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $data = $validator->validated();

        // Upload file
        if ($request->hasFile('guide_file')) {
            $file = $request->file('guide_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/guide_files', $filename);
            $data['guide_file'] = $filename;
        }

        // Clean embed YouTube
        if ($request->filled('guide_video')) {
            $cleanEmbed = preg_replace('/\s*(width|height)="[^"]*"/i', '', $request->guide_video);
            $data['guide_video'] = strip_tags($cleanEmbed, '<iframe>');
        }

        $category = QuestionCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Kategori Pertanyaan berhasil ditambahkan.',
            'data' => $category
        ]);
    }

    public function edit($id)
    {
        $category = QuestionCategory::find($id);

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'data' => $category]);
    }

    public function update(Request $request, $id)
    {
        $category = QuestionCategory::find($id);

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 404);
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:question_categories,name,' . $id,
            'level' => 'required|in:Umum,Siswa,Guru',
            'solution_text' => 'nullable|string',
            'guide_file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:2048',
            'guide_video' => 'nullable|string|max:2000',
            'category_status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $data = $validator->validated();

        // Upload file baru
        if ($request->hasFile('guide_file')) {
            if ($category->guide_file && Storage::exists('public/guide_files/' . $category->guide_file)) {
                Storage::delete('public/guide_files/' . $category->guide_file);
            }

            $file = $request->file('guide_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/guide_files', $filename);
            $data['guide_file'] = $filename;
        }

        // Update embed TAPI jangan hapus embed lama kalau user tidak isi
        if ($request->filled('guide_video')) {
            $cleanEmbed = preg_replace('/\s*(width|height)="[^"]*"/i', '', $request->guide_video);
            $data['guide_video'] = strip_tags($cleanEmbed, '<iframe>');
        }

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Kategori Pertanyaan berhasil diperbarui.',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = QuestionCategory::find($id);

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 404);
        }

        if (CSRoom::where('question_categories_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena digunakan oleh Layanan Pelanggan aktif.',
            ], 409);
        }

        if (CSLog::where('question_categories_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena memiliki riwayat log.',
            ], 409);
        }

        if ($category->guide_file && Storage::exists('public/guide_files/' . $category->guide_file)) {
            Storage::delete('public/guide_files/' . $category->guide_file);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Kategori Pertanyaan berhasil dihapus.']);
    }
}
