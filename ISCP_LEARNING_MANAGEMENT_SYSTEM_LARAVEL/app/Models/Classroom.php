<?php

namespace App\Models;

use App\Models\Base\Classroom as BaseClassroom;

class Classroom extends BaseClassroom
{
	protected $fillable = [

		'serial_id',
		'name',
		'grade',
		'code'
	];
}
