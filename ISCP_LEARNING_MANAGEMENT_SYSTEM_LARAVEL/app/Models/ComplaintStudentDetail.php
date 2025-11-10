<?php

namespace App\Models;

use App\Models\Base\ComplaintStudentDetail as BaseComplaintStudentDetail;

class ComplaintStudentDetail extends BaseComplaintStudentDetail
{
	protected $fillable = [
		'complaint_message_student_id',
		'message_sender',
		'message_content',
		'sent_time'
	];
}
