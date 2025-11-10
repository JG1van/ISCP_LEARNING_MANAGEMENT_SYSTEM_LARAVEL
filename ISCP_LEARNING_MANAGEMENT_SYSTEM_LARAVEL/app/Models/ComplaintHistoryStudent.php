<?php

namespace App\Models;

use App\Models\Base\ComplaintHistoryStudent as BaseComplaintHistoryStudent;

class ComplaintHistoryStudent extends BaseComplaintHistoryStudent
{
	protected $fillable = [
		'issue_category_id',
		'reporter_student_id',
		'admin_id',
		'completion_time',
		'resolution_by',
		'notes'
	];
}
