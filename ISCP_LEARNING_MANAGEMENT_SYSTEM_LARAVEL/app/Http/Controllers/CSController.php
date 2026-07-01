<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Throwable;

use App\Models\QuestionCategory;
use App\Models\CSRoom;
use App\Models\CSLog;
use App\Models\CSMessage;
use App\Models\Student;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use App\Models\CSFile;
use Illuminate\Support\Facades\Validator;
class CSController extends Controller
{
    protected FirebaseService $firebase;

    private const STATUS_SISTEM = 'Sistem';
    private const STATUS_ADMIN = 'Admin';

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /* GENERATE KODE */
    private function generateCode(): string
    {
        return Carbon::now()->format('dm-Y') . '-' .
            Str::upper(Str::random(4)) . '-' .
            Str::upper(Str::random(4));
    }

    /* CEK KEPEMILIKAN ROOM */
    private function checkOwner(Request $request, CSRoom $room): bool
    {
        $user = auth()->user();

        if ($room->user_id && $user && $user->id == $room->user_id)
            return true;
        if ($room->student_id && session('student_id') == $room->student_id)
            return true;
        if (session()->get('public_room_' . $room->room_code))
            return true;
        return false;
    }

    /* HALAMAN AWAL */
    public function showCreateForm()
    {
        return view('layanan-pelanggan.index');
    }

    /* BUAT ROOM */
    public function createRoom(Request $request)
    {
        DB::beginTransaction();
        try {
            $room = CSRoom::create([
                'room_code' => $this->generateCode(),
                'question_categories_id' => null,
                'student_id' => session('student_id'),
                'user_id' => auth()->user()?->id,
                'admin_id' => null,
                'chat_status' => self::STATUS_SISTEM,
            ]);

            if (!auth()->check() && !session('student_id')) {
                session()->put('public_room_' . $room->room_code, true);
            }

            DB::commit();
            return redirect()->route('layanan-pelanggan.ruang_pesan', $room->room_code);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("createRoom: " . $e->getMessage());

            return back()->with('error', 'Gagal membuat ruang Layanan Pelanggan.');
        }
    }

    /* LOGIN SISWA */
    public function studentLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $student = Student::where('username', $request->username)
            ->where('password_text', $request->password)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.'
            ], 401);
        }

        $existingRoom = CSRoom::where('student_id', '!=', $student->id)
            ->whereNotNull('student_id')
            ->first();

        if ($existingRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan Pelanggan ini hanya dapat dibuka oleh akun pelapor yang membuatnya.'
            ], 403);
        }

        session()->put('student_id', $student->id);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.'
        ], 200);
    }

    /* TETAPKAN KATEGORI */
    public function assignCategory(Request $request, $code)
    {
        $request->validate([
            'category_id' => 'required|exists:question_categories,id'
        ]);

        $room = CSRoom::where('room_code', $code)->firstOrFail();

        if (!$this->checkOwner($request, $room)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $room->update([
                'question_categories_id' => $request->category_id,
                'chat_status' => self::STATUS_SISTEM,
                'updated_at' => Carbon::now('Asia/Jakarta') // ⬅️ WAKTU INDONESIA
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dipilih.'
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("assignCategory: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menetapkan kategori.'
            ], 500);
        }
    }


    /* VIEW CHAT */
    public function userChat(Request $request, $code)
    {
        $room = CSRoom::where('room_code', $code)->firstOrFail();

        if (!$this->checkOwner($request, $room)) {
            return back()->with('error', 'Tidak memiliki akses.');
        }

        $role = session('role');
        $tabUmum = QuestionCategory::where('category_status', 'Aktif')->where('level', 'Umum')->get();
        $tabSiswa = $role === 'guru' ? collect() : QuestionCategory::where('category_status', 'Aktif')->where('level', 'Siswa')->get();
        $tabGuru = $role === 'siswa' ? collect() : QuestionCategory::where('category_status', 'Aktif')->where('level', 'Guru')->get();


        return view('layanan-pelanggan.ruang_pesan', compact(
            'room',
            'tabUmum',
            'tabSiswa',
            'tabGuru',
        ));
    }
    public function sendTelegram($message)
    {
        $token = config('telegram.bot_token');
        $chat_id = config('telegram.chat_id');

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $response = Http::asForm()->post($url, [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ]);

        if ($response->failed()) {
            Log::error("Telegram Send Failed: " . $response->body());
        }

        return $response->json();
    }



    /* SET ADMIN MODE */
    public function setAdminStatus($id)
    {
        $room = CSRoom::findOrFail($id);
        $room->update(['chat_status' => self::STATUS_ADMIN]);

        try {
            // Kirim pesan otomatis ke Firebase
            $this->firebase->pushMessage($room->id, [
                'id' => time(),
                'sender' => 'Sistem',
                'message' => 'Terima kasih telah menghubungi layanan ini.<br>
Silakan jelaskan masalah Anda selagi menunggu admin.<br>
Jika dalam 5 menit admin belum merespons, silakan tekan tombol panggilan ulang. Gunakan fitur ini dengan bijak.',
                'time' => now('Asia/Jakarta')->format("H:i"),
                'ts' => now('Asia/Jakarta')->timestamp * 1000,
                'files' => []
            ]);
        } catch (Throwable $e) {
            Log::error("Firebase auto-admin message failed: " . $e->getMessage());
        }


        try {
            $this->sendTelegram("
🔔 <b>PESAN BARU MASUK!</b>

<b>Kode Ruangan:</b> {$room->room_code}
<b>Waktu:</b> " . now('Asia/Jakarta')->format('d-m-Y H:i') . "

Pelapor baru saja menekan tombol <b>Mulai Percakapan</b>.
Admin diminta untuk segera meninjau dan merespons.

<i>Respon cepat meningkatkan kepuasan pengguna.</i>
");

        } catch (Throwable $e) {
            Log::error("Telegram notification failed: " . $e->getMessage());
        }


        return response()->json([
            'success' => true,
            'message' => 'Status admin aktif.'
        ]);
    }

    /* LANJUTKAN */
    public function continueCS(Request $request)
    {
        $request->validate([
            'room_code' => 'required|string'
        ]);

        $room = CSRoom::where('room_code', $request->room_code)->first();

        if (!$room) {
            return back()->with('error', 'Kode ruangan tidak ditemukan.');
        }

        // Buat session ulang agar bisa akses dari browser lain
        session()->put('public_room_' . $room->room_code, true);

        return redirect()->route('layanan-pelanggan.ruang_pesan', $room->room_code);
    }

    public function panggilLagi($roomId)
    {
        $room = CSRoom::findOrFail($roomId);

        try {
            // 📩 KIRIM NOTIFIKASI TELEGRAM
            $this->sendTelegram("
🔔 <b>PANGGILAN ULANG!</b>

<b>Kode Ruangan:</b> {$room->room_code}
<b>Waktu:</b> " . now('Asia/Jakarta')->format('d-m-Y H:i') . "

Pelapor menekan tombol <b>Panggilan Ulang</b> karena belum ada respon admin.

<i>Mohon admin segera memeriksa dan merespons.</i>
        ");
        } catch (Throwable $e) {
            Log::error("Telegram 'Panggil Lagi' failed: " . $e->getMessage());
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Notifikasi panggilan ulang terkirim.'
        ]);
    }

    /* SELESAIKAN */
    public function userFinish($code)
    {
        $room = CSRoom::where('room_code', $code)->firstOrFail();

        DB::beginTransaction();
        try {

            // 1. KIRIM PESAN SISTEM PALING AWAL
            $nowWIB = Carbon::now('Asia/Jakarta');

            try {
                $this->firebase->pushMessage($room->id, [
                    'id' => time(),
                    'sender' => 'Sistem',
                    'message' => 'Layanan ini telah diselesaikan oleh Pelapor.',
                    'time' => $nowWIB->format("Y-m-d H:i:s"), // <-- PERBAIKAN FULL TIME
                    'ts' => $nowWIB->timestamp * 1000,
                    'files' => []
                ]);
            } catch (\Throwable $e) {
                Log::error('Firebase final message push failed', [
                    'room_id' => $room->id,
                    'exception' => $e
                ]);
            }

            // 2. Ambil chat dari Firebase
            try {
                $chat = $this->firebase->getChatHistory($room->id);
            } catch (\Throwable $e) {
                Log::error('Firebase getChatHistory failed', [
                    'room_id' => $room->id,
                    'exception' => $e
                ]);
                $chat = ['hasChat' => false, 'messages' => []];
            }

            $hasChat = $chat['hasChat'];
            $messages = $chat['messages'] ?? [];

            // Urutkan berdasarkan timestamp
            usort($messages, fn($a, $b) => ($a['ts'] ?? 0) <=> ($b['ts'] ?? 0));

            // 3. PROSES CATATAN PERCAPAKAN
            if ($room->chat_status === self::STATUS_ADMIN) {

                if ($hasChat) {
                    $logText = [];

                    foreach ($messages as $msg) {

                        // Tentukan waktu pesan
                        if (!empty($msg['ts']) && is_numeric($msg['ts'])) {
                            $msgTime = Carbon::createFromTimestamp($msg['ts'] / 1000)
                                ->setTimezone('Asia/Jakarta')
                                ->format('d/m/Y H:i');
                        } elseif (!empty($msg['time'])) {
                            try {
                                $msgTime = Carbon::parse($msg['time'])
                                    ->timezone('Asia/Jakarta')
                                    ->format('d/m/Y H:i');
                            } catch (\Throwable) {
                                $msgTime = '-';
                            }
                        } else {
                            $msgTime = '-';
                        }

                        // Isi log
                        $logText[] = "[" . $msgTime . "] "
                            . ($msg['sender'] ?? 'Unknown') . ": "
                            . ($msg['message'] ?? '');
                    }

                    $notes = implode("\n", $logText);

                } else {
                    $notes = "Tidak ada percakapan.";
                }

                $resolutionBy = 'Admin';

            } else {
                $notes = "Terselesaikan otomatis oleh Sistem.";
                $resolutionBy = 'Sistem';
            }

            // 4. Simpan Log Penyelesaian
            CSLog::create([
                'question_categories_id' => $room->question_categories_id,
                'admin_id' => $room->admin_id,
                'completion_time' => $nowWIB,
                'resolution_by' => $resolutionBy,
                'notes' => $notes,
            ]);
            // 5. Hapus file
            try {

                $dir = "CS/room_{$room->id}";
                $basePath = public_path("storage/$dir");

                // Ambil semua file terkait
                $files = CSFile::where('room_id', $room->id)->get();

                foreach ($files as $f) {

                    // Jika file_path sudah mengandung CS/
                    if (str_contains($f->file_path, 'CS/')) {
                        $relativePath = $f->file_path;
                    } else {
                        $relativePath = "$dir/{$f->file_path}";
                    }

                    $fullPath = public_path("storage/" . $relativePath);

                    if (file_exists($fullPath)) {
                        unlink($fullPath); // HAPUS FILE REAL
                    }
                }

                // Hapus record DB
                CSFile::where('room_id', $room->id)->delete();

                // Hapus folder jika masih ada
                if (is_dir($basePath)) {
                    rmdir($basePath);
                }

            } catch (\Throwable $e) {
                Log::error('Gagal menghapus lampiran', [
                    'room_id' => $room->id,
                    'exception' => $e->getMessage()
                ]);
            }

            // 6. Hapus Room dari Firebase
            try {
                $this->firebase->deleteRoom($room->id);
            } catch (\Throwable $e) {
                Log::error('Firebase deleteRoom failed', [
                    'room_id' => $room->id,
                    'exception' => $e
                ]);
            }

            // 7. Hapus Room dari Database
            try {
                $room->delete();
            } catch (\Throwable $e) {
                Log::error('Room delete failed', [
                    'room_id' => $room->id,
                    'exception' => $e
                ]);
                throw $e;
            }

            DB::commit();

            // 8. Logout User
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('layanan-pelanggan.index')
                ->with('success', 'Layanan telah terselesaikan. Terima kasih telah menggunakan layanan kami.');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error("finish room", [
                'exception' => $e,
                'room_code' => $code
            ]);

            return back()->with(
                'error',
                app()->environment('local')
                ? 'Gagal menyelesaikan Layanan Pelanggan: ' . $e->getMessage()
                : 'Gagal menyelesaikan Layanan Pelanggan.'
            );
        }
    }




    public function upload(Request $request, $roomId)
    {
        // VALIDASI CUSTOM
        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'required|mimes:jpg,jpeg,png,webp|max:5000',
            ],
            [
                'file.required' => 'File wajib diupload.',
                'file.mimes' => 'Format file harus JPG, JPEG, PNG, atau WEBP.',
                'file.max' => 'Ukuran file maksimal 5MB.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // CEK ROOM
        $room = CSRoom::find($roomId);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room tidak ditemukan.'
            ], 404);
        }

        try {
            $ext = $request->file('file')->getClientOriginalExtension();
            $nextNumber = CSFile::where('room_id', $room->id)->count() + 1;
            $newName = 'gambar_' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT) . '.' . $ext;
            $folder = "CS/room_{$room->id}";
            $path = $request->file('file')->storeAs($folder, $newName, 'public');
            $file = CSFile::create([
                'room_id' => $room->id,
                'file_path' => $newName
            ]);
            //  KIRIM PESAN KE FIREBASE
            $nowWIB = now('Asia/Jakarta');

            $this->firebase->pushMessage($room->id, [
                'id' => time(),
                'sender' => 'Pelapor',
                'message' => "Mengirim file: {$newName}",
                'time' => $nowWIB->format("Y-m-d H:i:s"),
                'ts' => $nowWIB->timestamp * 1000,
                'files' => []
            ]);

            return response()->json([
                'success' => true,
                'url' => asset("storage/$folder/$newName"),
                'file' => [
                    'id' => $file->id,
                    'name' => $newName,
                    'time' => $file->created_at->format('Y-m-d H:i')
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function listFiles($roomId)
    {
        $files = CSFile::where('room_id', $roomId)
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function ($f) use ($roomId) {

                $folder = "CS/room_$roomId";

                return [
                    'id' => $f->id,
                    'url' => asset("storage/$folder/" . $f->file_path),
                    'name' => $f->file_path,
                    'ext' => strtolower(pathinfo($f->file_path, PATHINFO_EXTENSION)),
                ];
            });

        return response()->json([
            'success' => true,
            'files' => $files
        ]);
    }

}
