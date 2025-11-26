<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Http\JsonResponse;
use App\Models\ComplaintRoom;
use App\Models\ComplaintLog;
use App\Services\FirebaseService;

class ComplaintAdminController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->middleware('auth');

        $this->firebase = $firebase;
    }


    public function adminChat($code)
    {
        $room = ComplaintRoom::where('complaint_code', $code)->firstOrFail();

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

        return view('admin.pengaduan.ruang_pesan', compact('room'));
    }


    public function adminDelete(Request $request, $code): JsonResponse
    {
        $room = ComplaintRoom::where('complaint_code', $code)->firstOrFail();

        DB::beginTransaction();
        try {
            // Hapus chat di Firebase (tidak membuat error fatal)
            try {
                $this->firebase->deleteRoom($room->id);
            } catch (Throwable $e) {
                Log::error("Firebase deleteRoom error: " . $e->getMessage());
                // lanjutkan, karena kita tetap mau hapus room di DB
            }

            $roomId = $room->id;
            $room->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'id' => $roomId,
                'message' => 'Pengaduan berhasil dihapus.'
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("adminDelete error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pengaduan.'
            ], 500);
        }
    }

    public function adminIndex()
    {
        $data = ComplaintRoom::with(['user', 'student', 'complaint_category'])
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admin.pengaduan.index', compact('data'));
    }


    public function logsIndex()
    {
        $data = ComplaintLog::with(['complaint_category', 'admin'])
            ->orderBy('completion_time', 'DESC')
            ->get();

        $categories = \App\Models\ComplaintCategory::all();
        $admins = \App\Models\Admin::all();

        return view('admin.pengaduan.riwayat', compact('data', 'categories', 'admins'));
    }

    public function deleteLog($id)
    {
        try {
            $log = ComplaintLog::findOrFail($id);
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
