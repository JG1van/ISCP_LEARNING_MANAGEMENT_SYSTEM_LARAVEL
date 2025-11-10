<?php

namespace App\Models;

use App\Models\Base\OnlineMeeting as BaseOnlineMeeting;

class OnlineMeeting extends BaseOnlineMeeting
{
	protected $fillable = [
		'serial_id',
		'classroom_id',
		'user_id',
		'title',
		'description',
		'meeting_code',
		'meeting_link',
		'platform',
		'start_time',
		'end_time',
		'status'
	];
}
