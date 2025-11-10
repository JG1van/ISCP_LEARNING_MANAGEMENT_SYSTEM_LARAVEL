<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Admin;
use App\Models\ComplaintStudentDetail;
use App\Models\IssueCategory;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintMessagesStudent
 * 
 * @property int $id
 * @property int $reporter_student_id
 * @property int $admin_id
 * @property int $issue_category_id
 * @property Carbon $report_time
 * @property string $chat_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Admin $admin
 * @property IssueCategory $issue_category
 * @property Student $student
 * @property Collection|ComplaintStudentDetail[] $complaint_student_details
 *
 * @package App\Models\Base
 */
class ComplaintMessagesStudent extends Model
{
	protected $table = 'complaint_messages_students';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'reporter_student_id' => 'int',
		'admin_id' => 'int',
		'issue_category_id' => 'int',
		'report_time' => 'datetime'
	];

	public function admin()
	{
		return $this->belongsTo(Admin::class);
	}

	public function issue_category()
	{
		return $this->belongsTo(IssueCategory::class);
	}

	public function student()
	{
		return $this->belongsTo(Student::class, 'reporter_student_id');
	}

	public function complaint_student_details()
	{
		return $this->hasMany(ComplaintStudentDetail::class, 'complaint_message_student_id');
	}
}
