<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Konstruktor
     * Menerapkan middleware auth dan is_admin agar hanya admin yang dapat mengakses controller ini.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Menampilkan daftar produk.
     * Jika menggunakan AJAX, mengirim data JSON semua produk untuk DataTables.
     * Jika tidak, menampilkan halaman utama dengan tabel produk.
     */
    public function index(Request $request)
    {
        $products = Product::orderBy('id', 'asc')->get();

        // Ambil semua lesson sekali saja untuk efisiensi
        $lessons = Lesson::pluck('name', 'id')->toArray();

        // Tambahkan properti baru ke setiap produk
        foreach ($products as $product) {
            $lessonIds = json_decode($product->lesson_id) ?? [];
            $product->lesson_names = collect($lessonIds)
                ->map(fn($id) => $lessons[$id] ?? null)
                ->filter()
                ->values()
                ->toArray();
        }

        if ($request->ajax()) {
            return response()->json(['data' => $products]);
        }

        return view('admin.produk.index', [
            'data' => $products
        ]);
    }

    /**
     * Tampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        $lessons = Lesson::all(); // ambil semua lesson, bisa diubah sesuai kebutuhan
        return view('admin.produk.create', compact('lessons'));
    }
    /**
     * Menyimpan data produk baru.
     * Memvalidasi input, menyimpan data produk ke database, dan mengembalikan respons JSON.
     */
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:50|unique:products,name',
                'grade' => 'required|string|max:50',
                'grade_category' => 'required|array',
                'grade_category.*' => 'string',
                'semester' => 'required|string|max:50',
                'materi_json' => 'nullable|string',
            ],
            [
                'name.required' => 'Nama produk wajib diisi.',
                'name.unique' => 'Nama produk sudah digunakan, silakan pilih nama lain.',
                'grade.required' => 'Kelas wajib diisi.',
                'semester.required' => 'Semester wajib diisi.',

            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first())
                ->withInput();
        }

        try {
            $data = $validator->validated();

            // Decode JSON dari hidden input materi_json
            $materi = json_decode($data['materi_json'], true);


            // Ambil daftar ID lesson
            $lessonIds = collect($materi)->pluck('id')->toArray();

            // Encode grade_category
            $data['grade_category'] = json_encode($data['grade_category'] ?? []);

            // Encode lesson_id
            $data['lesson_id'] = json_encode($lessonIds);



            // Simpan data produk
            Product::create([

                'name' => $data['name'],
                'grade' => $data['grade'],
                'semester' => $data['semester'],
                'grade_category' => $data['grade_category'],
                'lesson_id' => $data['lesson_id'],
            ]);

            return redirect()->route('admin.produk.index')
                ->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', true)
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Menampilkan form edit produk.
     */
    public function edit($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->route('admin.produk.index')->with('error', 'Produk tidak ditemukan.');
        }

        // Ambil semua lesson untuk pilihan materi
        $lessons = Lesson::select('id', 'name')->orderBy('id')->get();

        // Decode JSON dari database agar bisa ditampilkan di form
        $product->lesson_id = json_decode($product->lesson_id, true) ?? [];
        $product->grade_category = json_decode($product->grade_category, true) ?? [];

        return view('admin.produk.edit', compact('product', 'lessons'));
    }

    /**
     * Memperbarui data produk yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->route('admin.produk.index')
                ->with('error', 'Produk tidak ditemukan.');
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:50|unique:products,name,' . $id,
                'grade' => 'required|string|max:50',
                'grade_category' => 'required|array',
                'grade_category.*' => 'string',
                'semester' => 'required|string|max:50',
                'materi_json' => 'nullable|string',
            ],
            [
                'name.required' => 'Nama produk wajib diisi.',
                'name.unique' => 'Nama produk sudah digunakan oleh produk lain.',
                'grade.required' => 'Kelas wajib diisi.',
                'semester.required' => 'Semester wajib diisi.',
                'materi_json.required' => 'Data materi wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first())
                ->withInput();
        }

        try {
            $data = $validator->validated();

            // Decode JSON dari input hidden materi_json
            $materi = json_decode($data['materi_json'], true);

            $lessonIds = collect($materi)->pluck('id')->toArray();

            // Siapkan data yang akan diupdate
            $dataToUpdate = [
                'name' => $data['name'],
                'grade' => $data['grade'],
                'semester' => $data['semester'],
                'grade_category' => json_encode($data['grade_category']),
                'lesson_id' => json_encode($lessonIds),
            ];

            $product->update($dataToUpdate);

            return redirect()->route('admin.produk.index')
                ->with('success', 'Produk berhasil diperbarui.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', true)
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Menghapus produk.
     * Menghapus data produk berdasarkan ID dari database.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->route('admin.produk.index')
                ->with('error', 'Produk tidak ditemukan.');
        }

        // Cek apakah produk masih digunakan di tabel lain
        $relatedData = [];

        if (\App\Models\Serial::where('product_id', $id)->exists()) {
            $relatedData[] = 'serial';
        }

        // Jika masih terhubung dengan data lain
        if (!empty($relatedData)) {
            $list = implode(', ', $relatedData);
            return redirect()->route('admin.produk.index')
                ->with('error', "Produk tidak dapat dihapus karena masih digunakan oleh data: {$list}.");
        }

        try {
            $product->delete();

            return redirect()->route('admin.produk.index')
                ->with('success', 'Produk berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->route('admin.produk.index')
                    ->with('error', 'Produk tidak dapat dihapus karena masih terhubung dengan data lain.');
            }

            return redirect()->route('admin.produk.index')
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('admin.produk.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


}
