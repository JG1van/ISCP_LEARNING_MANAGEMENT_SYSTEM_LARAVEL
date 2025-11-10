<?php

namespace App\Models;

use App\Models\Base\Classroom as BaseClassroom;

class Classroom extends BaseClassroom
{
	protected $fillable = [
		'id',
		'serial_id',
		'name',
		'grade',
		'code'
	];
}
