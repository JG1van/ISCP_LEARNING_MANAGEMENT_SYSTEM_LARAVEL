<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Throwable;

use App\Models\ComplaintCategory;
use App\Models\ComplaintRoom;
use App\Models\ComplaintLog;
use App\Models\ComplaintMessage;
use App\Models\Student;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
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
    private function checkOwner(Request $request, ComplaintRoom $room): bool
    {
        $user = auth()->user();

        if ($room->user_id && $user && $user->id == $room->user_id)
            return true;
        if ($room->student_id && session('student_id') == $room->student_id)
            return true;
        if (session()->get('public_room_' . $room->complaint_code))
            return true;
        return false;
    }

    /* HALAMAN AWAL */
    public function showCreateForm()
    {
        return view('pengaduan.index');
    }

    /* BUAT ROOM */
    public function createRoom(Request $request)
    {
        DB::beginTransaction();
        try {
            $room = ComplaintRoom::create([
                'complaint_code' => $this->generateCode(),
                'complaint_category_id' => null,
                'student_id' => session('student_id'),
                'user_id' => auth()->user()?->id,
                'admin_id' => null,
                'chat_status' => self::STATUS_SISTEM,
            ]);

            if (!auth()->check() && !session('student_id')) {
                session()->put('public_room_' . $room->complaint_code, true);
            }

            DB::commit();
            return redirect()->route('pengaduan.ruang_pesan', $room->complaint_code);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("createRoom: " . $e->getMessage());

            return back()->with('error', 'Gagal membuat ruang pengaduan.');
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

        $existingRoom = ComplaintRoom::where('student_id', '!=', $student->id)
            ->whereNotNull('student_id')
            ->first();

        if ($existingRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaduan ini hanya dapat dibuka oleh akun pelapor yang membuatnya.'
            ], 403);
        }

        session()->put('student_id', $student->id);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.'
        ], 200);
    }

    /* ASSIGN CATEGORY */
    public function assignCategory(Request $request, $code)
    {
        $request->validate([
            'category_id' => 'required|exists:complaint_categories,id'
        ]);

        $room = ComplaintRoom::where('complaint_code', $code)->firstOrFail();

        if (!$this->checkOwner($request, $room)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $room->update([
                'complaint_category_id' => $request->category_id,
                'chat_status' => self::STATUS_SISTEM,
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
        $room = ComplaintRoom::where('complaint_code', $code)->firstOrFail();

        if (!$this->checkOwner($request, $room)) {
            return back()->with('error', 'Tidak memiliki akses.');
        }

        $role = session('role');
        $tabUmum = ComplaintCategory::where('category_status', 'Aktif')->where('level', 'Umum')->get();
        $tabSiswa = $role === 'guru' ? collect() : ComplaintCategory::where('category_status', 'Aktif')->where('level', 'Siswa')->get();
        $tabGuru = $role === 'siswa' ? collect() : ComplaintCategory::where('category_status', 'Aktif')->where('level', 'Guru')->get();


        return view('pengaduan.ruang_pesan', compact(
            'room',
            'tabUmum',
            'tabSiswa',
            'tabGuru',
        ));
    }
    public function sendTelegram($message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chat_id = env('TELEGRAM_CHAT_ID');

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
        $room = ComplaintRoom::findOrFail($id);
        $room->update(['chat_status' => self::STATUS_ADMIN]);

        try {
            // Kirim pesan otomatis ke Firebase
            $this->firebase->pushMessage($room->id, [
                'id' => time(),
                'sender' => 'Sistem',
                'message' => 'Terima kasih telah menghubungi layanan ini.<br>Silakan jelaskan masalah Anda selagi menunggu admin.',
                'time' => now('Asia/Jakarta')->format("H:i"),
                'ts' => now('Asia/Jakarta')->timestamp * 1000,
                'files' => []
            ]);
        } catch (Throwable $e) {
            Log::error("Firebase auto-admin message failed: " . $e->getMessage());
        }


        try {
            $this->sendTelegram("
🔔 <b>PENGADUAN BARU MASUK!</b>

<b>Kode Ruangan:</b> {$room->complaint_code}

Pengguna baru saja menekan tombol <b>Hubungi Admin</b>.
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

    /* LANJUTKAN PENGADUAN */
    public function continueComplaint(Request $request)
    {
        $request->validate([
            'complaint_code' => 'required|string'
        ]);

        $room = ComplaintRoom::where('complaint_code', $request->complaint_code)->first();

        if (!$room) {
            return back()->with('error', 'Kode pengaduan tidak ditemukan.');
        }

        // Buat session ulang agar bisa akses dari browser lain
        session()->put('public_room_' . $room->complaint_code, true);

        return redirect()->route('pengaduan.ruang_pesan', $room->complaint_code);
    }


    /* SELESAIKAN */
    public function userFinish($code)
    {
        $room = ComplaintRoom::where('complaint_code', $code)->firstOrFail();

        DB::beginTransaction();
        try {
            // Ambil chat dari Firebase
            $chat = $this->firebase->getChatHistory($room->id);
            $hasChat = $chat['hasChat'];
            $messages = $chat['messages'];

            // Waktu WIB
            $nowWIB = Carbon::now('Asia/Jakarta');

            //---------------------------------------------------------
            // PROSES CATATAN PERCAPAKAN
            //---------------------------------------------------------
            if ($room->chat_status === self::STATUS_ADMIN) {

                if ($hasChat) {
                    $logText = [];

                    foreach ($messages as $msg) {

                        //-----------------------------------------------
                        // 1) Jika Firebase menggunakan ts (UNIX ms timestamp)
                        //-----------------------------------------------
                        if (isset($msg['ts']) && is_numeric($msg['ts'])) {
                            $msgTime = Carbon::createFromTimestamp($msg['ts'] / 1000)
                                ->setTimezone('Asia/Jakarta')
                                ->format('d/m/Y H:i');
                        }

                        //-----------------------------------------------
                        // 2) Jika Firebase hanya mengirim string "time"
                        //-----------------------------------------------
                        elseif (!empty($msg['time'])) {
                            try {
                                $msgTime = Carbon::parse($msg['time'])
                                    ->timezone('Asia/Jakarta')
                                    ->format('d/m/Y H:i');
                            } catch (\Throwable $e) {
                                $msgTime = '-';
                            }
                        }

                        //-----------------------------------------------
                        // 3) Tidak ada timestamp valid
                        //-----------------------------------------------
                        else {
                            $msgTime = '-';
                        }

                        //-----------------------------------------------
                        // Susun catatan ke string
                        //-----------------------------------------------
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
                $notes = "Pengaduan selesai otomatis oleh Sistem.";
                $resolutionBy = 'Sistem';
            }

            //---------------------------------------------------------
            // SIMPAN LOG KE DATABASE (dengan WIB)
            //---------------------------------------------------------
            ComplaintLog::create([
                'complaint_category_id' => $room->complaint_category_id,
                'admin_id' => $room->admin_id,
                'completion_time' => $nowWIB,
                'resolution_by' => $resolutionBy,
                'notes' => $notes,
            ]);

            //---------------------------------------------------------
            // HAPUS Firebase + Room
            //---------------------------------------------------------
            $this->firebase->deleteRoom($room->id);
            $room->delete();

            DB::commit();

            //---------------------------------------------------------
            // LOGOUT USER
            //---------------------------------------------------------
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('pengaduan.index')
                ->with('success', 'Pengaduan selesai & akun logout otomatis.');

        } catch (Throwable $e) {

            DB::rollBack();
            Log::error("finish room: " . $e->getMessage());

            return back()->with('error', 'Gagal menyelesaikan pengaduan.');
        }
    }

}
