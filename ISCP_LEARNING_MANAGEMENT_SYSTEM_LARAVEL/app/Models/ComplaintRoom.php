<?php

namespace App\Models;

use App\Models\Base\ComplaintRoom as BaseComplaintRoom;

class ComplaintRoom extends BaseComplaintRoom
{
	protected $fillable = [
		'complaint_code',
		'complaint_category_id',
		'student_id',
		'user_id',
		'admin_id',
		'chat_status'
	];
}
