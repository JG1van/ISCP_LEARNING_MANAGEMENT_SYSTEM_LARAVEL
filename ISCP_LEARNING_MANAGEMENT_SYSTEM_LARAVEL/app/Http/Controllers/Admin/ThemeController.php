<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Theme;
use App\Models\Subtheme;
use App\Models\LessonItem;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public function index($lesson_id)
    {
        // $lesson = Lesson::with([
        //     'themes' => fn($q) => $q->orderBy('theme'),
        //     'themes.subthemes' => fn($q) => $q->orderBy('subtheme'),
        //     'themes.subthemes.lessonItems' => fn($q) => $q->orderBy('number'),
        // ])->findOrFail($lesson_id);
        $lesson = Lesson::with('themes.subthemes.lessonItems')->findOrFail($lesson_id);
        return view('admin.pelajaran.materi', compact('lesson'));
    }

    // -----------------------------
    // THEME
    // -----------------------------
    public function storeTheme(Request $request, $lesson_id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:200',
            ], [
                'name.required' => 'Nama tema wajib diisi.',
                'name.string' => 'Nama tema harus berupa teks.',
                'name.max' => 'Nama tema maksimal 200 karakter.',
            ]);

            // Auto increment manual untuk ID
            $newId = (Theme::orderByDesc('id')->value('id') ?? 0) + 1;

            // Cari nomor tema terakhir untuk pelajaran ini
            $lastTheme = Theme::where('lesson_id', $lesson_id)
                ->orderByDesc('theme')
                ->first();

            $nextThemeNumber = $lastTheme ? $lastTheme->theme + 1 : 1;

            // Simpan tema baru
            $theme = Theme::create([
                'id' => $newId,
                'lesson_id' => $lesson_id,
                'theme' => $nextThemeNumber,
                'name' => $request->name,
            ]);

            return response()->json(['success' => true, 'data' => $theme]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function editTheme($lesson_id, $id)
    {
        $data = Theme::find($id);
        if (!$data) {
            return response()->json(['success' => false]);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function updateTheme(Request $request, $lesson_id, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:200',
                'theme' => 'required|integer|min:1',
            ], [
                'name.required' => 'Nama tema wajib diisi.',
                'name.string' => 'Nama tema harus berupa teks.',
                'name.max' => 'Nama tema maksimal 200 karakter.',

                'theme.required' => 'Nomor tema wajib diisi.',
                'theme.integer' => 'Nomor tema harus berupa angka.',
                'theme.min' => 'Nomor tema minimal 1.',
            ]);

            $data = Theme::findOrFail($id);

            // Cek duplikat nomor tema pada lesson yang sama (exclude current)
            $exists = Theme::where('lesson_id', $lesson_id)
                ->where('theme', $request->theme)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Nomor tema sudah digunakan pada pelajaran ini. Silakan pilih nomor lain.']);
            }

            $data->update([
                'name' => $request->name,
                'theme' => $request->theme,
            ]);

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'errors' => $ve->errors()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroyTheme($lesson_id, $id)
    {
        Theme::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // -----------------------------
    // SUBTHEME
    // -----------------------------
    public function storeSubtheme(Request $request, $lesson_id)
    {
        try {
            $request->validate([
                'theme_id' => 'required|integer|exists:themes,id',
                'name' => 'required|string|max:200',
            ], [
                'theme_id.required' => 'Tema wajib dipilih.',
                'theme_id.integer' => 'Tema tidak valid.',
                'theme_id.exists' => 'Tema tidak ditemukan.',

                'name.required' => 'Nama subtema wajib diisi.',
                'name.string' => 'Nama subtema harus berupa teks.',
                'name.max' => 'Nama subtema maksimal 200 karakter.',
            ]);

            // Auto increment manual untuk ID
            $newId = (Subtheme::orderByDesc('id')->value('id') ?? 0) + 1;

            // Cari nomor subtema terakhir untuk theme ini
            $lastSub = Subtheme::where('theme_id', $request->theme_id)
                ->orderByDesc('subtheme')
                ->first();

            $nextSubNumber = $lastSub ? $lastSub->subtheme + 1 : 1;

            // Simpan subtema baru
            $sub = Subtheme::create([
                'id' => $newId,
                'lesson_id' => $lesson_id,
                'theme_id' => $request->theme_id,
                'subtheme' => $nextSubNumber,
                'name' => $request->name,
            ]);

            return response()->json(['success' => true, 'data' => $sub]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'errors' => $ve->errors()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function editSubtheme($lesson_id, $id)
    {
        $data = Subtheme::find($id);
        if (!$data) {
            return response()->json(['success' => false]);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function updateSubtheme(Request $request, $lesson_id, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:200',
                'subtheme' => 'required|integer|min:1',
                'theme_id' => 'required|integer|exists:themes,id',
            ], [
                'name.required' => 'Nama subtema wajib diisi.',
                'name.string' => 'Nama subtema harus berupa teks.',
                'name.max' => 'Nama subtema maksimal 200 karakter.',

                'subtheme.required' => 'Nomor subtema wajib diisi.',
                'subtheme.integer' => 'Nomor subtema harus berupa angka.',
                'subtheme.min' => 'Nomor subtema minimal 1.',

                'theme_id.required' => 'Tema wajib dipilih.',
                'theme_id.integer' => 'Tema tidak valid.',
                'theme_id.exists' => 'Tema tidak ditemukan.',
            ]);

            $data = Subtheme::findOrFail($id);

            // Cek duplikat nomor subtema dalam theme yang sama (exclude current)
            $exists = Subtheme::where('theme_id', $request->theme_id)
                ->where('subtheme', $request->subtheme)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Nomor subtema sudah digunakan pada tema ini. Silakan pilih nomor lain.']);
            }

            $data->update([
                'name' => $request->name,
                'subtheme' => $request->subtheme,
                'theme_id' => $request->theme_id,
            ]);

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'errors' => $ve->errors()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroySubtheme($lesson_id, $id)
    {
        Subtheme::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // -----------------------------
    // LESSON ITEM (MATERI)
    // -----------------------------
    public function storeLessonItem(Request $request, $lesson_id)
    {
        try {
            $request->validate([
                'theme_id' => 'required|integer|exists:themes,id',
                'subtheme_id' => 'required|integer|exists:subthemes,id',
                'title' => 'required|string',
                'embed' => 'nullable|string',
            ], [
                'theme_id.required' => 'Tema wajib dipilih.',
                'theme_id.integer' => 'Tema tidak valid.',
                'theme_id.exists' => 'Tema tidak ditemukan.',

                'subtheme_id.required' => 'Subtema wajib dipilih.',
                'subtheme_id.integer' => 'Subtema tidak valid.',
                'subtheme_id.exists' => 'Subtema tidak ditemukan.',

                'title.required' => 'Judul materi wajib diisi.',
                'title.string' => 'Judul materi harus berupa teks.',

                'embed.string' => 'Embed harus berupa teks HTML/iframe.',
            ]);

            //   Bersihkan width & height dari iframe
            $cleanEmbed = $request->embed
                ? preg_replace('/\s*(width|height)="[^"]*"/i', '', $request->embed)
                : null;

            // Auto increment manual untuk ID
            $newId = (LessonItem::orderByDesc('id')->value('id') ?? 0) + 1;

            // Nomor urut item berdasarkan subtema
            $lastItem = LessonItem::where('subtheme_id', $request->subtheme_id)
                ->orderByDesc('number')
                ->first();

            $nextNumber = $lastItem ? $lastItem->number + 1 : 1;

            // Simpan item
            $item = LessonItem::create([
                'id' => $newId,
                'lesson_id' => $lesson_id,
                'theme_id' => $request->theme_id,
                'subtheme_id' => $request->subtheme_id,
                'admin_id' => auth()->id() ?? 1,
                'number' => $nextNumber,
                'title' => $request->title,
                'embed' => $cleanEmbed,
            ]);

            return response()->json(['success' => true, 'data' => $item]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'errors' => $ve->errors()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function editLessonItem($lesson_id, $id)
    {
        $data = LessonItem::select('id', 'title', 'embed', 'theme_id', 'subtheme_id', 'number')
            ->find($id);
        if (!$data) {
            return response()->json(['success' => false]);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function updateLessonItem(Request $request, $lesson_id, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'embed' => 'nullable|string',
                'theme_id' => 'required|integer|exists:themes,id',
                'subtheme_id' => 'required|integer|exists:subthemes,id',
                'number' => 'required|integer|min:1',
            ], [
                'title.required' => 'Judul materi wajib diisi.',
                'title.string' => 'Judul materi harus berupa teks.',

                'embed.string' => 'Embed harus berupa teks HTML/iframe.',

                'theme_id.required' => 'Tema wajib dipilih.',
                'theme_id.integer' => 'Tema tidak valid.',
                'theme_id.exists' => 'Tema tidak ditemukan.',

                'subtheme_id.required' => 'Subtema wajib dipilih.',
                'subtheme_id.integer' => 'Subtema tidak valid.',
                'subtheme_id.exists' => 'Subtema tidak ditemukan.',

                'number.required' => 'Nomor materi wajib diisi.',
                'number.integer' => 'Nomor materi harus berupa angka.',
                'number.min' => 'Nomor materi minimal 1.',
            ]);

            $data = LessonItem::findOrFail($id);

            // Hapus width & height dari iframe
            $cleanEmbed = $request->embed
                ? preg_replace('/\s*(width|height)="[^"]*"/i', '', $request->embed)
                : null;

            // Cek duplikat nomor di subtema target (exclude current item)
            $exists = LessonItem::where('subtheme_id', $request->subtheme_id)
                ->where('number', $request->number)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor materi sudah digunakan pada subtema ini. Silakan pilih nomor lain.'
                ]);
            }

            // Update data termasuk admin_id baru
            $data->update([
                'title' => $request->title,
                'embed' => $cleanEmbed,
                'theme_id' => $request->theme_id,
                'subtheme_id' => $request->subtheme_id,
                'number' => $request->number,
                'admin_id' => auth()->id(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'errors' => $ve->errors()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroyLessonItem($lesson_id, $id)
    {
        LessonItem::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
