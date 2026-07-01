<?php

namespace App\Models;

use App\Models\Base\CsRoom as BaseCsRoom;

class CsRoom extends BaseCsRoom
{
	protected $fillable = [
		'room_code',
		'question_categories_id',
		'student_id',
		'user_id',
		'admin_id',
		'chat_status'
	];
}
