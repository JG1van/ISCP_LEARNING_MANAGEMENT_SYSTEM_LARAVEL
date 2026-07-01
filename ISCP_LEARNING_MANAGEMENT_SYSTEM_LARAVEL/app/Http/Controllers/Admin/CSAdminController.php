<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Http\JsonResponse;
use App\Models\CSRoom;
use App\Models\CSLog;
use App\Services\FirebaseService;
use App\Models\CSFile;
use Illuminate\Support\Facades\Validator;
class CSAdminController extends Controller
{
    public const ALLOWED_ROLES = [1, 2, 5];
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->middleware('auth');

        $this->firebase = $firebase;
    }


    public function adminChat($code)
    {
        $room = CSRoom::where('room_code', $code)->first();

        if (!$room) {
            return view('admin.layanan-pelanggan.ruang_pesan', [
                'room' => null,
                'messages' => collect()
            ]);
        }

        $admin = auth()->user();

        if ($room->admin_id !== null && $room->admin_id !== $admin->id) {
            return abort(403, 'Room ini sudah ditangani oleh admin lain.');
        }
        if ($room->admin_id === null) {
            $room->update([
                'admin_id' => $admin->id,
                'chat_status' => 'Admin'
            ]);
        }

        return view('admin.layanan-pelanggan.ruang_pesan', compact('room'));
    }


    public function adminDelete(Request $request, $code): JsonResponse
    {
        $room = CSRoom::where('room_code', $code)->firstOrFail();

        DB::beginTransaction();
        try {
            // 1. Hapus chat di Firebase
            try {
                $this->firebase->deleteRoom($room->id);
            } catch (Throwable $e) {
                Log::error("Firebase deleteRoom error: " . $e->getMessage());
            }

            // 2. Hapus file lampiran
            try {

                $dir = "CS/room_{$room->id}";
                $basePath = public_path("storage/$dir");

                // Ambil semua file terkait
                $files = CSFile::where('room_id', $room->id)->get();

                foreach ($files as $f) {

                    if (str_contains($f->file_path, 'CS/')) {
                        $relativePath = $f->file_path;
                    } else {
                        $relativePath = "$dir/{$f->file_path}";
                    }

                    $fullPath = public_path("storage/" . $relativePath);

                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }

                // Hapus record DB
                CSFile::where('room_id', $room->id)->delete();

                // Hapus folder jika masih ada
                if (is_dir($basePath)) {
                    rmdir($basePath);
                }

            } catch (\Throwable $e) {
                Log::error('Gagal menghapus lampiran (adminDelete)', [
                    'room_id' => $room->id,
                    'exception' => $e->getMessage()
                ]);
            }

            // 3. Hapus Room dari DB
            $roomId = $room->id;
            $room->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'id' => $roomId,
                'message' => 'Layanan Pelanggan berhasil dihapus.'
            ], 200);

        } catch (Throwable $e) {

            DB::rollBack();

            Log::error("adminDelete error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus layanan-pelanggan.'
            ], 500);
        }
    }

    public function adminUpload(Request $request, $roomId)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'required|mimes:jpg,jpeg,png,webp|max:5000',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

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

            // KIRIM CHAT FILE
            $nowWIB = now('Asia/Jakarta');

            $this->firebase->pushMessage($room->id, [
                'id' => time(),
                'sender' => 'Admin(' . auth()->user()->username . '#' . auth()->user()->id . ')',
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
    public function adminIndex()
    {
        $data = CSRoom::with(['user', 'student', 'question_category'])
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admin.layanan-pelanggan.index', compact('data'));
    }


    public function logsIndex()
    {
        $data = CSLog::with(['question_category', 'admin'])
            ->orderBy('completion_time', 'DESC')
            ->get();

        $categories = \App\Models\QuestionCategory::all();
        $admins = \App\Models\Admin::all();

        return view('admin.layanan-pelanggan.riwayat', compact('data', 'categories', 'admins'));
    }

    public function deleteLog($id)
    {
        try {
            $log = CSLog::findOrFail($id);
            $log->delete();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat berhasil dihapus.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus riwayat.'
            ], 500);
        }
    }

}
