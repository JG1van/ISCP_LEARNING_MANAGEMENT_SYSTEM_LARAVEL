<?php

namespace App\Models;

use App\Models\Base\ExercisePoint as BaseExercisePoint;

class ExercisePoint extends BaseExercisePoint
{
	protected $fillable = [
		'serial_id',
		'exercise_id',
		'student_id',
		'answer',
		'competence_point',
		'exercise_point'
	];
}
