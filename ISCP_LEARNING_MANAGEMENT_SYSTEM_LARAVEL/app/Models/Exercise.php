<?php

namespace App\Models;

use App\Models\Base\Exercise as BaseExercise;

class Exercise extends BaseExercise
{
	protected $fillable = [

		'lesson_id',
		'serial_id',
		'exercise_type_id',
		'title',
		'is_admin'
	];
}
