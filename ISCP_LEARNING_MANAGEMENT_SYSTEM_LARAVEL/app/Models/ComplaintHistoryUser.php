<?php

namespace App\Models;

use App\Models\Base\ComplaintHistoryUser as BaseComplaintHistoryUser;

class ComplaintHistoryUser extends BaseComplaintHistoryUser
{
	protected $fillable = [
		'issue_category_id',
		'reporter_user_id',
		'admin_id',
		'completion_time',
		'resolution_by',
		'notes'
	];
}
