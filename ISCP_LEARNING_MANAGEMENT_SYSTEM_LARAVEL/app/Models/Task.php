<?php

namespace App\Models;

use App\Models\Base\Task as BaseTask;

class Task extends BaseTask
{
	protected $fillable = [
		'serial_id',
		'post_id',
		'student_id',
		'description',
		'attachment',
		'point'
	];
}
