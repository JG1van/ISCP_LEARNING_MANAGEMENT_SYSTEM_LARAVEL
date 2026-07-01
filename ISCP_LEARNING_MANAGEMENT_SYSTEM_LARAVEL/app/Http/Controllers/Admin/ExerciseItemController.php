<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExerciseItem;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\Competence;
use App\Models\ExerciseModel;
use App\Models\ExerciseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExerciseItemController extends Controller
{
    public const ALLOWED_ROLES = [1, 2, 4];
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tampilkan daftar soal dari exercise tertentu.
     */
    public function index($lesson_id, $exercise_id)
    {
        // 🔹 Ambil data kompetensi untuk dropdown KD
        $competences = Competence::where('lesson_id', $lesson_id)->get();

        // 🔹 Ambil data pelajaran & soal
        $lesson = Lesson::find($lesson_id);
        $exercise = Exercise::find($exercise_id);

        if (!$lesson || !$exercise) {
            return redirect()->back()->with('error', 'Data pelajaran atau soal tidak ditemukan.');
        }

        // 🔹 Ambil daftar soal soal + relasi kompetensi & pembuat
        $exerciseItems = ExerciseItem::with(['competence', 'admin', 'user'])
            ->where('exercise_id', $exercise_id)
            ->orderBy('id', 'asc') // urutkan berdasarkan kolom ID
            ->get();

        // 🔹 Ambil daftar model soal
        $exerciseModels = \App\Models\ExerciseModel::all();

        // 🔹 Kirim ke view
        return view('admin.pelajaran.soal_index', compact(
            'lesson',
            'exercise',
            'competences',
            'exerciseItems',
            'exerciseModels'
        ));
    }



    /**
     * Form tambah soal baru.
     */
    public function create($lesson_id, $exercise_id)
    {
        $exercise = Exercise::findOrFail($exercise_id);
        $competences = Competence::where('lesson_id', $lesson_id)->get();
        $models = ExerciseModel::all();
        $exerciseType = \App\Models\ExerciseType::find($exercise->exercise_type_id);

        return view('admin.pelajaran.soal_create', [
            'lesson_id' => $lesson_id,
            'exercise_id' => $exercise_id,
            'exercise_type_id' => $exercise->exercise_type_id ?? null,
            'exerciseType' => $exerciseType,
            'competences' => $competences,
            'models' => $models,
        ]);
    }


    /**
     * Simpan soal baru.
     */
    public function store(Request $request, $lesson_id, $exercise_id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'question' => 'required|string',
                'selection' => 'nullable', // bisa array atau JSON string
                'answer' => 'required',
                'exercise_model_id' => 'required|integer|exists:exercise_models,id',
                'competence_id' => 'nullable|integer|exists:competences,id',
            ],
            [
                'question.required' => 'Pertanyaan wajib diisi.',
                'answer.required' => 'Jawaban wajib diisi.',
                'exercise_model_id.required' => 'Model soal wajib dipilih.',
                'exercise_model_id.exists' => 'Model soal tidak valid.',
                'competence_id.exists' => 'Kompetensi tidak ditemukan.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first())
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $lesson_id, $exercise_id) {
                $exercise = Exercise::findOrFail($exercise_id);


                // Nomor urut soal terakhir
                $lastNumber = ExerciseItem::where('exercise_id', $exercise_id)
                    ->lockForUpdate()
                    ->max('exercise_number') ?? 0;

                // --- Proses SELECTION ---
                $selection = $request->input('selection');
                if (is_string($selection)) {
                    $decoded = json_decode($selection, true);
                    $selection = is_array($decoded) ? json_encode($decoded) : json_encode([]);
                } elseif (is_array($selection)) {
                    $selection = json_encode($selection);
                } else {
                    $selection = json_encode([]);
                }

                // --- Proses ANSWER ---
                $answer = $request->input('answer');
                if (is_array($answer)) {
                    $answer = json_encode($answer);
                } else {
                    $answer = json_encode([$answer]);
                }

                // Cek apakah ada pilihan jawaban
                $hasSelection = !empty($request->input('selection')) && $request->input('selection') !== '[]';

                // Simpan ke database
                ExerciseItem::create([

                    'admin_id' => auth()->id() ?? 5, // fallback ID admin default
                    'user_id' => null,
                    'lesson_id' => $lesson_id,
                    'exercise_id' => $exercise_id,
                    'exercise_type_id' => $exercise->exercise_type_id ?? ($request->input('exercise_type_id') ?? 1),
                    'exercise_model_id' => $request->input('exercise_model_id'),
                    'competence_id' => $request->input('competence_id'),
                    'exercise_choice' => $hasSelection ? 1 : 0,
                    'exercise_number' => $lastNumber + 1,
                    'question' => $request->input('question'),
                    'selection' => $selection,
                    'answer' => $answer,
                    'is_user' => $request->input('is_user') ?? 0,
                ]);
            });

            return redirect()->route('admin.pelajaran.judul_soal.soal.index', [
                'lesson_id' => $lesson_id,
                'exercise_id' => $exercise_id,
            ])->with('success', 'Soal berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan soal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Form edit soal.
     */
    public function edit($lesson_id, $exercise_id, $soal_id)
    {
        // Ambil lesson & exercise (cek valid)
        $lesson = Lesson::find($lesson_id);
        $exercise = Exercise::find($exercise_id);

        if (!$lesson || !$exercise) {
            return redirect()->back()->with('error', 'Data pelajaran atau soal tidak ditemukan.');
        }

        // Ambil item soal beserta relasi (competence, admin, user)
        $item = ExerciseItem::with(['competence', 'admin', 'user'])
            ->findOrFail($soal_id);

        // Ambil data pendukung untuk form edit
        $competences = Competence::where('lesson_id', $lesson_id)->get();
        $models = ExerciseModel::all();
        $exerciseType = \App\Models\ExerciseType::find($exercise->exercise_type_id);

        // Pastikan selection adalah array untuk dipakai di view (jika masih string, decode)
        if (is_string($item->selection) && $item->selection !== '') {
            $decoded = json_decode($item->selection, true);
            $item->selection = is_array($decoded) ? $decoded : [];
        } elseif (is_null($item->selection)) {
            $item->selection = [];
        }
        // Pastikan answer juga dikembalikan ke bentuk array (view edit kamu mengharapkan array)
        if (is_string($item->answer) && $item->answer !== '') {
            $decAns = json_decode($item->answer, true);
            $item->answer = is_array($decAns) ? $decAns : [$item->answer];
        } elseif (is_null($item->answer)) {
            $item->answer = [];
        }

        // Kirim semua ke view
        return view('admin.pelajaran.soal_edit', compact(
            'lesson',
            'exercise',
            'item',
            'competences',
            'models',
            'exerciseType'
        ));
    }



    /**
     * Update soal.
     */

    public function update(Request $request, $lesson_id, $exercise_id, $soal_id)
    {
        $item = ExerciseItem::find($soal_id);

        if (!$item) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan.');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'question' => 'required|string',
                'selection' => 'nullable',
                'answer' => 'required',
                'exercise_model_id' => 'required|integer|exists:exercise_models,id',
                'competence_id' => 'nullable|integer|exists:competences,id',
                'exercise_number' => 'required|integer|min:1',
            ],
            [
                'question.required' => 'Pertanyaan wajib diisi.',
                'answer.required' => 'Jawaban wajib diisi.',
                'exercise_model_id.required' => 'Model soal wajib dipilih.',
                'exercise_number.required' => 'Nomor soal wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first())
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $item) {

                // --- Selection ---
                $selection = $request->input('selection');
                if (is_string($selection)) {
                    $decoded = json_decode($selection, true);
                    $selection = is_array($decoded) ? json_encode($decoded) : json_encode([]);
                } elseif (is_array($selection)) {
                    $selection = json_encode($selection);
                } else {
                    $selection = json_encode([]);
                }

                // --- Answer ---
                $answer = $request->input('answer');
                if (is_array($answer)) {
                    $answer = json_encode($answer);
                } else {
                    $answer = json_encode([$answer]);
                }

                // --- Update data ---
                $item->question = $request->question;
                $item->selection = $selection;
                $item->answer = $answer;
                $item->exercise_model_id = $request->exercise_model_id;
                $item->exercise_number = $request->exercise_number;
                $item->exercise_choice = !empty($selection) ? 1 : 0;
                $item->admin_id = auth()->id();

                // ✅ Pastikan competence_id ikut diubah (termasuk jadi null)
                $item->competence_id = $request->filled('competence_id')
                    ? $request->competence_id
                    : null;

                $item->save();
            });

            return redirect()->route('admin.pelajaran.judul_soal.soal.index', [
                'lesson_id' => $lesson_id,
                'exercise_id' => $exercise_id,
            ])->with('success', 'Soal berhasil diperbarui.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui soal: ' . $e->getMessage())
                ->withInput();
        }
    }



    /**
     * Hapus soal.
     */
    public function destroy($lesson_id, $exercise_id, $soal_id)
    {
        $item = ExerciseItem::find($soal_id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak ditemukan.',
            ]);
        }

        try {
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Soal berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus soal: ' . $e->getMessage(),
            ]);
        }
    }
}
