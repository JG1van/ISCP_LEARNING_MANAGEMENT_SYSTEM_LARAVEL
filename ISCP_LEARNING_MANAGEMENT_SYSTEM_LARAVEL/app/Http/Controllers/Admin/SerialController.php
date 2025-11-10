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

class SerialController extends Controller
{
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
            $validator = \Validator::make(
                $request->all(),
                [
                    'product_id' => 'required|exists:products,id',
                    'paket' => 'required|integer|min:1|max:9',
                    'active' => 'required|integer|min:1|max:120',
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

            // Cegah duplikasi user–produk
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

            $lastId = Serial::max('id');
            $newId = $lastId ? $lastId + 1 : 1;

            $serial = Serial::create([
                'id' => $newId,
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'serial' => $this->generateSerial(),
                'paket' => $request->paket,
                'active' => $request->active,
                'expired_at' => null,
            ]);

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
                'paket' => 'required|integer|min:1|max:9',
                'active' => 'nullable|integer|min:1|max:120',
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

        try {
            $serial->delete();

            return response()->json([
                'success' => true,
                'message' => 'Serial berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Serial tidak dapat dihapus karena masih terhubung dengan data lain.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus serial: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate serial unik
     */
    private function generateSerial()
    {
        do {
            $code = strtoupper(Str::random(5) . '-' . Str::random(5) . '-' . Str::random(5) . '-' . Str::random(5));
        } while (Serial::where('serial', $code)->exists());

        return $code;
    }
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

            // ✅ Batas maksimal TIMESTAMP
            $maxTimestamp = Carbon::create(2038);

            if ($newExpired->greaterThan($maxTimestamp)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perpanjangan gagal karena tanggal kedaluwarsa melebihi batas maksimal sistem, yaitu tahun 2038. Batas maksimal tipe TIMESTAMP adalah tahun 2038. Silakan kurangi jumlah bulan perpanjangan.',
                ], 422);
            }

            // Jika aman, simpan perubahan
            $serial->expired_at = $newExpired;
            $serial->active = (int) $serial->active + (int) $request->extend_months;
            $serial->save();

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

}

