<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\{
    IssueCategory,
    ComplaintMessagesUser,
    ComplaintMessagesStudent,
    ComplaintUserDetail,
    ComplaintStudentDetail,
    ComplaintUserFile,
    ComplaintStudentFile,
    ComplaintHistoryUser,
    ComplaintHistoryStudent
};

class ComplaintController extends Controller
{
    /**
     * 🟩 1. Menampilkan daftar laporan pengaduan
     */
    public function index()
    {
        $userComplaints = ComplaintMessagesUser::with('reporterUser', 'category', 'admin')
            ->orderByDesc('created_at')
            ->get();

        $studentComplaints = ComplaintMessagesStudent::with('reporterStudent', 'category', 'admin')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'users' => $userComplaints,
            'students' => $studentComplaints,
        ]);
    }

    /**
     * 🟩 2. Menampilkan daftar kategori pengaduan aktif
     */
    public function categories()
    {
        $categories = IssueCategory::where('issue_category_status', 'Active')
            ->orderByRaw("FIELD(level, 'Low', 'Medium', 'High')")
            ->get();

        return response()->json($categories);
    }

    /**
     * 🟩 3. Menambah kategori baru
     */
    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'issue_category_name' => 'required|string|max:100',
            'level' => 'required|in:Low,Medium,High',
            'solution_text' => 'required|string',
            'guide_file' => 'nullable|file|mimes:pdf,docx,mp4|max:10240',
            'guide_video' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = new IssueCategory();
        $category->issue_category_name = $request->issue_category_name;
        $category->level = $request->level;
        $category->solution_text = $request->solution_text;
        $category->issue_category_status = 'Active';

        if ($request->hasFile('guide_file')) {
            $path = $request->file('guide_file')->store('guides', 'public');
            $category->guide_file = $path;
        }

        if ($request->guide_video) {
            $category->guide_video = $request->guide_video;
        }

        $category->save();

        return response()->json(['message' => 'Kategori berhasil ditambahkan']);
    }

    /**
     * 🟩 4. Update kategori pengaduan
     */
    public function updateCategory(Request $request, $id)
    {
        $category = IssueCategory::findOrFail($id);

        $category->update([
            'issue_category_name' => $request->issue_category_name ?? $category->issue_category_name,
            'level' => $request->level ?? $category->level,
            'solution_text' => $request->solution_text ?? $category->solution_text,
            'issue_category_status' => $request->issue_category_status ?? $category->issue_category_status,
        ]);

        if ($request->hasFile('guide_file')) {
            $path = $request->file('guide_file')->store('guides', 'public');
            $category->guide_file = $path;
        }

        if ($request->guide_video) {
            $category->guide_video = $request->guide_video;
        }

        $category->save();

        return response()->json(['message' => 'Kategori berhasil diperbarui']);
    }

    /**
     * 🟩 5. Menampilkan detail percakapan pengaduan
     */
    public function showChat($id, $type)
    {
        if ($type === 'user') {
            $details = ComplaintUserDetail::where('complaint_message_user_id', $id)
                ->with('files')
                ->orderBy('sent_time', 'asc')
                ->get();
        } elseif ($type === 'student') {
            $details = ComplaintStudentDetail::where('complaint_message_student_id', $id)
                ->with('files')
                ->orderBy('sent_time', 'asc')
                ->get();
        } else {
            return response()->json(['error' => 'Tipe tidak valid'], 400);
        }

        return response()->json($details);
    }

    /**
     * 🟩 6. Mengirim balasan chat (oleh CS atau System)
     */
    public function sendReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:user,student',
            'complaint_id' => 'required|integer',
            'message_content' => 'required|string',
            'files.*' => 'nullable|file|max:5120',
            'admin_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type = $request->type;
        $adminId = $request->admin_id ?? 0;
        $sender = $adminId == 0 ? 'System' : 'CS';

        if ($type === 'user') {
            $detail = ComplaintUserDetail::create([
                'complaint_message_user_id' => $request->complaint_id,
                'message_sender' => $sender,
                'message_content' => $request->message_content,
                'sent_time' => Carbon::now(),
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('complaint_files', 'public');
                    ComplaintUserFile::create([
                        'complaint_user_detail_id' => $detail->id,
                        'complaint_file' => $path
                    ]);
                }
            }

            $status = $sender === 'System' ? 'System' : 'CS';
            ComplaintMessagesUser::where('id', $request->complaint_id)->update(['chat_status' => $status]);
        }

        if ($type === 'student') {
            $detail = ComplaintStudentDetail::create([
                'complaint_message_student_id' => $request->complaint_id,
                'message_sender' => $sender,
                'message_content' => $request->message_content,
                'sent_time' => Carbon::now(),
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('complaint_files', 'public');
                    ComplaintStudentFile::create([
                        'complaint_student_detail_id' => $detail->id,
                        'complaint_file' => $path
                    ]);
                }
            }

            $status = $sender === 'System' ? 'System' : 'CS';
            ComplaintMessagesStudent::where('id', $request->complaint_id)->update(['chat_status' => $status]);
        }

        return response()->json(['message' => 'Balasan berhasil dikirim']);
    }

    /**
     * 🟩 7. Menandai laporan selesai (manual / otomatis)
     */
    public function markAsResolved($id, $type)
    {
        DB::beginTransaction();

        try {
            if ($type === 'user') {
                $complaint = ComplaintMessagesUser::findOrFail($id);

                ComplaintHistoryUser::create([
                    'issue_category_id' => $complaint->issue_category_id,
                    'reporter_user_id' => $complaint->reporter_user_id,
                    'admin_id' => $complaint->admin_id ?? 0,
                    'completion_time' => Carbon::now(),
                    'resolution_by' => $complaint->admin_id == 0 ? 'System' : 'CS',
                    'notes' => $complaint->admin_id == 0 ? 'Selesai oleh sistem' : 'Diselesaikan oleh CS',
                ]);

                // Hapus semua detail & file
                $detailIds = ComplaintUserDetail::where('complaint_message_user_id', $id)->pluck('id');
                ComplaintUserFile::whereIn('complaint_user_detail_id', $detailIds)->delete();
                ComplaintUserDetail::where('complaint_message_user_id', $id)->delete();
                $complaint->delete();
            }

            if ($type === 'student') {
                $complaint = ComplaintMessagesStudent::findOrFail($id);

                ComplaintHistoryStudent::create([
                    'issue_category_id' => $complaint->issue_category_id,
                    'reporter_student_id' => $complaint->reporter_student_id,
                    'admin_id' => $complaint->admin_id ?? 0,
                    'completion_time' => Carbon::now(),
                    'resolution_by' => $complaint->admin_id == 0 ? 'System' : 'CS',
                    'notes' => $complaint->admin_id == 0 ? 'Selesai oleh sistem' : 'Diselesaikan oleh CS',
                ]);

                // Hapus semua detail & file
                $detailIds = ComplaintStudentDetail::where('complaint_message_student_id', $id)->pluck('id');
                ComplaintStudentFile::whereIn('complaint_student_detail_id', $detailIds)->delete();
                ComplaintStudentDetail::where('complaint_message_student_id', $id)->delete();
                $complaint->delete();
            }

            DB::commit();
            return response()->json(['message' => 'Laporan berhasil dipindahkan ke riwayat']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menyelesaikan laporan', 'debug' => $e->getMessage()], 500);
        }
    }
}
