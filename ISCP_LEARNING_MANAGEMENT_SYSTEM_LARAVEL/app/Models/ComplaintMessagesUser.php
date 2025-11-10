<?php

namespace App\Models;

use App\Models\Base\ComplaintMessagesUser as BaseComplaintMessagesUser;

class ComplaintMessagesUser extends BaseComplaintMessagesUser
{
	protected $fillable = [
		'reporter_user_id',
		'admin_id',
		'issue_category_id',
		'report_time',
		'chat_status'
	];
}
