<?php

namespace App\Models;

use App\Models\Base\ComplaintMessage as BaseComplaintMessage;

class ComplaintMessage extends BaseComplaintMessage
{
	protected $fillable = [
		'complaint_room_id',
		'message_sender',
		'message_content',
		'sent_time'
	];
}
