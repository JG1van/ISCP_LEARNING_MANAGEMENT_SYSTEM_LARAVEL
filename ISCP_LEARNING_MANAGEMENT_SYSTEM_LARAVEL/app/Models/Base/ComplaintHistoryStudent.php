<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Admin;
use App\Models\IssueCategory;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintHistoryStudent
 * 
 * @property int $id
 * @property int $issue_category_id
 * @property int $reporter_student_id
 * @property int $admin_id
 * @property Carbon $completion_time
 * @property string $resolution_by
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Admin $admin
 * @property IssueCategory $issue_category
 * @property Student $student
 *
 * @package App\Models\Base
 */
class ComplaintHistoryStudent extends Model
{
	protected $table = 'complaint_history_students';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'issue_category_id' => 'int',
		'reporter_student_id' => 'int',
		'admin_id' => 'int',
		'completion_time' => 'datetime'
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
}
