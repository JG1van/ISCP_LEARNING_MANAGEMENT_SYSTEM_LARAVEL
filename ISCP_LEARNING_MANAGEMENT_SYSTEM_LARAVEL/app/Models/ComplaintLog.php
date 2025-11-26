<?php

namespace App\Models;

use App\Models\Base\ComplaintLog as BaseComplaintLog;

class ComplaintLog extends BaseComplaintLog
{
	protected $fillable = [
		'complaint_category_id',
		'admin_id',
		'completion_time',
		'resolution_by',
		'notes'
	];
}
