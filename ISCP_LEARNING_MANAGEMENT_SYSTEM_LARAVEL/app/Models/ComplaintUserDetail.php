<?php

namespace App\Models;

use App\Models\Base\ComplaintUserDetail as BaseComplaintUserDetail;

class ComplaintUserDetail extends BaseComplaintUserDetail
{
	protected $fillable = [
		'complaint_message_user_id',
		'message_sender',
		'message_content',
		'sent_time'
	];
}
