<?php

namespace App\Models;

use App\Models\Base\Report as BaseReport;

class Report extends BaseReport
{
	protected $fillable = [
		'serial_id',
		'student_id',
		'report',
		'img'
	];
}
