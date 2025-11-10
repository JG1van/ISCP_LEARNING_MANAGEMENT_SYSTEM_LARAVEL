<?php

namespace App\Models;

use App\Models\Base\ExerciseType as BaseExerciseType;

class ExerciseType extends BaseExerciseType
{
	protected $fillable = [
		'id',
		'kode',
		'name'
	];
}
