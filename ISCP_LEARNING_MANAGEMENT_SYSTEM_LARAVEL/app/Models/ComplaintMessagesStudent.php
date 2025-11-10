<?php

namespace App\Models;

use App\Models\Base\ComplaintMessagesStudent as BaseComplaintMessagesStudent;

class ComplaintMessagesStudent extends BaseComplaintMessagesStudent
{
	protected $fillable = [
		'reporter_student_id',
		'admin_id',
		'issue_category_id',
		'report_time',
		'chat_status'
	];
}
