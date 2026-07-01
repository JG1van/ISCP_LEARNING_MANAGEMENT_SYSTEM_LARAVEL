<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Theme;
use App\Models\Subtheme;
use App\Models\LessonItem;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class ImportMateriController extends Controller
{
    public const ALLOWED_ROLES = [1, 2, 4];
    public function index($lesson_id)
    {
        $lesson = Lesson::findOrFail($lesson_id);
        return view('admin.lesson.import', compact('lesson'));
    }

    /**
     * Proses upload dan import file Excel.
     */
    public function store(Request $request, $lesson_id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:4096',
        ]);

        $file = $request->file('file');
        $totalImported = 0;

        try {
            // Inisialisasi pembaca spreadsheet
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);

            if (method_exists($reader, 'setReadEmptyCells')) {
                $reader->setReadEmptyCells(false);
            }

            $spreadsheet = $reader->load($file->getRealPath());
        } catch (\Throwable $e) {
            return back()->withErrors([
                'file' => 'File tidak dapat dibaca. Pastikan format .xlsx atau valid. (' . $e->getMessage() . ')'
            ]);
        }

        DB::beginTransaction();

        try {
            foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                $rows = $sheet->toArray(null, true, true, true);

                // Lewati header baris pertama
                foreach (array_slice($rows, 1) as $row) {
                    // Bersihkan data
                    foreach ($row as &$cell) {
                        if (is_string($cell)) {
                            $cell = trim(preg_replace('/\s+/', ' ', $cell));
                        }
                    }
                    unset($cell);

                    // Validasi minimal kolom A–E
                    if (
                        empty($row['A']) || empty($row['B']) ||
                        empty($row['C']) || empty($row['D']) || empty($row['E'])
                    ) {
                        continue; // skip baris kosong/tidak lengkap
                    }

                    $themeName = $row['A'];
                    $subthemeName = $row['B'];
                    $title = $row['D'];
                    $embed = $row['E'];

                    // Bersihkan tag iframe dari atribut ukuran
                    $embed = preg_replace('/\s(width|height)="[^"]*"/i', '', $embed);

                    // ====== THEME ======
                    $theme = Theme::where('lesson_id', $lesson_id)
                        ->where('name', $themeName)
                        ->first();

                    if (!$theme) {
                        $theme = new Theme();
                        $theme->id = (Theme::max('id') ?? 0) + 1;
                        $theme->lesson_id = $lesson_id;
                        $theme->name = $themeName;
                        $theme->theme = (Theme::where('lesson_id', $lesson_id)->max('theme') ?? 0) + 1;
                        $theme->save();
                    }

                    // ====== SUBTHEME ======
                    $subtheme = Subtheme::where('theme_id', $theme->id)
                        ->where('name', $subthemeName)
                        ->first();

                    if (!$subtheme) {
                        $subtheme = new Subtheme();
                        $subtheme->id = (Subtheme::max('id') ?? 0) + 1;
                        $subtheme->lesson_id = $lesson_id;
                        $subtheme->theme_id = $theme->id;
                        $subtheme->name = $subthemeName;
                        $subtheme->subtheme = (Subtheme::where('theme_id', $theme->id)->max('subtheme') ?? 0) + 1;
                        $subtheme->save();
                    }

                    // ====== LESSON ITEM ======
                    $lessonItem = new LessonItem();
                    $lessonItem->id = (LessonItem::max('id') ?? 0) + 1;
                    $lessonItem->lesson_id = $lesson_id;
                    $lessonItem->theme_id = $theme->id;
                    $lessonItem->subtheme_id = $subtheme->id;
                    $lessonItem->admin_id = auth()->id() ?? 1;
                    $lessonItem->number = (LessonItem::where('subtheme_id', $subtheme->id)->max('number') ?? 0) + 1;
                    $lessonItem->title = $title;
                    $lessonItem->embed = $embed;
                    $lessonItem->save();

                    $totalImported++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal import materi: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['file' => 'Gagal mengimpor data: ' . $e->getMessage()]);
        }

        return redirect()
            ->back()
            ->with('success', "  Import selesai! Total {$totalImported} materi berhasil dimasukkan.");
    }
}
