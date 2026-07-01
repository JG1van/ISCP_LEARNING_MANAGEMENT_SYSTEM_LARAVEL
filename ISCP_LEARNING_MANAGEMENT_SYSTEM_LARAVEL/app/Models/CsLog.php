<?php

namespace App\Models;

use App\Models\Base\CsLog as BaseCsLog;

class CsLog extends BaseCsLog
{
	protected $fillable = [
		'question_categories_id',
		'admin_id',
		'completion_time',
		'resolution_by',
		'notes'
	];
}
