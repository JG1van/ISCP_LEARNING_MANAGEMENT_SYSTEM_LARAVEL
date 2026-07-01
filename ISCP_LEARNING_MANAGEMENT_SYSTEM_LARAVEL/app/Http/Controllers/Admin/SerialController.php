<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Serial;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\SerialLog;

class SerialController extends Controller
{
    public const ALLOWED_ROLES = [1, 2, 3];
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tampilkan daftar serial
     */
    public function index(Request $request)
    {
        // Jika request dari AJAX, kirim JSON
        if ($request->ajax()) {
            $data = Serial::with(['product', 'user'])
                ->orderBy('id', 'asc')
                ->get();
            return response()->json(['data' => $data]);
        }

        // Jika akses normal, kirim data ke view
        $data = Serial::with(['product', 'user'])->orderBy('id', 'asc')->get();
        $products = Product::orderBy('id', 'asc')->get();
        $users = User::orderBy('id', 'asc')->get();

        return view('admin.serial.index', compact('data', 'products', 'users'));
    }

    /**
     * Simpan serial baru
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = \Validator::make(
                $request->all(),
                [
                    'product_id' => 'required|exists:products,id',
                    'paket' => 'required|regex:/^[0-9]{1}$/',
                    'active' => 'required|regex:/^[0-9]{1,3}$/',
                    'user_id' => 'nullable|exists:users,id',
                ],
                [
                    'product_id.required' => 'Produk wajib dipilih.',
                    'product_id.exists' => 'Produk tidak ditemukan.',
                    'paket.required' => 'Jumlah paket wajib diisi.',
                    'active.required' => 'Lama aktif wajib diisi.',
                    'user_id.exists' => 'Pengguna tidak ditemukan.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            // Cegah user memiliki serial produk yang sama
            if ($request->user_id) {
                $exists = Serial::where('user_id', $request->user_id)
                    ->where('product_id', $request->product_id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pengguna ini sudah memiliki serial untuk produk yang sama.',
                    ], 409);
                }
            }

            // Generate serial unik
            $serialCode = $this->generateSerial();

            // Insert data serial
            $serial = Serial::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'serial' => $serialCode,
                'paket' => $request->paket,
                'active' => $request->active,
                'expired_at' => null,
            ]);

            // ============================================================
            // ⬇️ Tambahkan Log Baru
            // ============================================================
            SerialLog::create([
                'serial_id' => $serial->id,
                'active' => $serial->active,
                'status' => 'Baru',
            ]);
            // ============================================================

            return response()->json([
                'success' => true,
                'message' => 'Serial berhasil ditambahkan.',
                'data' => $serial,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan serial: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Generate serial anti-duplikasi
     */
    private function generateSerial()
    {
        do {
            $code = strtoupper(
                Str::random(5) . '-' .
                Str::random(5) . '-' .
                Str::random(5) . '-' .
                Str::random(5)
            );
        } while (Serial::where('serial', $code)->exists());

        return $code;
    }

    /**
     * Ambil data serial berdasarkan ID
     */
    public function edit($id)
    {
        $serial = Serial::with(['product', 'user'])->find($id);

        if (!$serial) {
            return response()->json([
                'success' => false,
                'message' => 'Serial tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $serial,
        ]);
    }

    /**
     * Update serial
     */
    public function update(Request $request, $id)
    {
        $serial = Serial::find($id);
        if (!$serial) {
            return response()->json(['success' => false, 'message' => 'Serial tidak ditemukan.'], 404);
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'product_id' => 'required|exists:products,id',
                'paket' => 'required|regex:/^[0-9]{1}$/',
                'active' => 'required|regex:/^[0-9]{1,3}$/',
                'user_id' => 'nullable|exists:users,id',
            ],
            [
                'product_id.required' => 'Produk wajib dipilih.',
                'product_id.exists' => 'Produk yang dipilih tidak valid.',

                'paket.required' => 'Jumlah paket wajib diisi.',
                'paket.integer' => 'Jumlah paket harus berupa angka.',
                'paket.min' => 'Jumlah paket minimal 1.',
                'paket.max' => 'Jumlah paket maksimal 9.',

                'active.integer' => 'Masa aktif harus berupa angka.',
                'active.min' => 'Masa aktif minimal 1 bulan.',
                'active.max' => 'Masa aktif maksimal 120 bulan.',

                'user_id.exists' => 'User yang dipilih tidak valid.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        if ($request->user_id) {
            $exists = Serial::where('user_id', $request->user_id)
                ->where('product_id', $request->product_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna ini sudah memiliki serial untuk produk yang sama.',
                ], 409);
            }
        }

        try {
            $serial->update([
                'product_id' => $request->product_id,
                'paket' => $request->paket,
                'active' => $request->active ?? $serial->active,
                'user_id' => $request->user_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Serial berhasil diperbarui.',
                'data' => $serial,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui serial: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hapus serial
     */
    public function destroy($id)
    {
        $serial = Serial::find($id);

        if (!$serial) {
            return response()->json([
                'success' => false,
                'message' => 'Serial tidak ditemukan.',
            ], 404);
        }

        // Cek apakah serial masih digunakan di tabel lain
        $relatedData = [];

        // Cek classrooms
        if (\App\Models\Classroom::where('serial_id', $id)->exists()) {
            $relatedData[] = 'kelas';
        }

        // Cek students
        if (\App\Models\Student::where('serial_id', $id)->exists()) {
            $relatedData[] = 'siswa';
        }

        // Cek reports
        if (\App\Models\Report::where('serial_id', $id)->exists()) {
            $relatedData[] = 'laporan';
        }

        // Cek tasks
        if (\App\Models\Task::where('serial_id', $id)->exists()) {
            $relatedData[] = 'tugas';
        }

        // Cek exercises
        if (\App\Models\Exercise::where('serial_id', $id)->exists()) {
            $relatedData[] = 'soal';
        }

        // Jika ada relasi yang masih digunakan
        if (!empty($relatedData)) {
            return response()->json([
                'success' => false,
                'message' => 'Serial tidak dapat dihapus karena masih digunakan pada: ' . implode(', ', $relatedData),
            ], 409);
        }

        try {
            $serial->delete();

            return response()->json([
                'success' => true,
                'message' => 'Serial berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus serial: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Generate serial unik
     */

    public function extend(Request $request, $id)
    {
        $serial = Serial::find($id);

        if (!$serial) {
            return response()->json([
                'success' => false,
                'message' => 'Serial tidak ditemukan.',
            ], 404);
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'extend_months' => 'required|integer|min:1|max:120',
            ],
            [
                'extend_months.required' => 'Jumlah bulan perpanjangan wajib diisi.',
                'extend_months.integer' => 'Perpanjangan harus berupa angka.',
                'extend_months.min' => 'Minimal perpanjangan 1 bulan.',
                'extend_months.max' => 'Maksimal perpanjangan 120 bulan (batas maksimal tipe TIMESTAMP adalah 19 Januari 2038).',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $now = Carbon::now();

            if (is_null($serial->expired_at)) {
                $newExpired = $now->copy()->addMonths($request->extend_months);
            } else {
                $expired = Carbon::parse($serial->expired_at);
                if ($expired->isPast()) {
                    $newExpired = $now->copy()->addMonths($request->extend_months);
                } else {
                    $newExpired = $expired->copy()->addMonths($request->extend_months);
                }
            }

            // Batas maksimal
            $maxTimestamp = Carbon::create(2038, 1, 19, 3, 14, 7);
            if ($newExpired->greaterThan($maxTimestamp)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perpanjangan gagal karena tanggal kedaluwarsa melebihi batas sistem.',
                ], 422);
            }

            // Simpan perubahan serial
            $serial->expired_at = $newExpired;
            $serial->active = (int) $serial->active + (int) $request->extend_months;
            $serial->save();

            // ============================================================
            // ⬇️ Insert Log Perpanjangan
            // ============================================================
            SerialLog::create([
                'serial_id' => $serial->id,
                'active' => $request->extend_months, // jumlah perpanjangan
                'status' => 'Perpanjang',
            ]);
            // ============================================================

            return response()->json([
                'success' => true,
                'message' => 'Masa aktif serial berhasil diperpanjang.',
                'expired_at' => $serial->expired_at,
                'active' => $serial->active,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperpanjang serial: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function riwayat()
    {
        $logs = SerialLog::with('serial')->latest()->get();

        return view('admin.serial.riwayat', compact('logs'));
    }

    public function sendSerialEmail(Request $request)
    {


        $request->validate([
            'serial_id' => 'required',
            'email' => 'required|email'
        ]);

        $serial = Serial::findOrFail($request->serial_id);

        $payload = [
            "sender" => [
                "name" => env('MAIL_FROM_NAME'),
                "email" => env('MAIL_FROM_ADDRESS'),
            ],
            "to" => [
                ["email" => $request->email]
            ],
            "subject" => "Informasi Serial Produk Anda",
            "htmlContent" => "
        <h3>Informasi Serial Produk Anda</h3>

        <p>Terima kasih telah berlangganan layanan kami.</p>
        <p>Berikut adalah informasi lengkap mengenai serial produk yang terdaftar pada akun Anda:</p>

        <p><b>Kode Serial Anda:</b></p>
        <h2 style='letter-spacing:3px; margin-top:5px;'>{$serial->serial}</h2>

        <br>

        <p><b>Detail Produk:</b></p>
        <p>Nama produk : <b>{$serial->product->name}</b></p>
        <p>Paket kelas: <b>{$serial->paket} kelas</b></p>
        <p>Durasi langganan: <b>{$serial->active} bulan</b></p>

        <br>

        <p>Jika Anda memerlukan bantuan atau memiliki pertanyaan lebih lanjut, tim kami siap membantu kapan saja.</p>

        <p>Hormat kami,<br>
        <b>" . env('MAIL_FROM_NAME') . "</b></p>
    "
        ];


        $response = Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Email berhasil dikirim']);
        }

        return response()->json([
            'success' => false,
            'message' => $response->json()['message'] ?? 'Gagal mengirim email (Unknown Error)'
        ], 500);


    }


}

