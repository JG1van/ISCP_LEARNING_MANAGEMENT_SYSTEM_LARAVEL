<?php

namespace App\Models;

use App\Models\Base\ExerciseModel as BaseExerciseModel;

class ExerciseModel extends BaseExerciseModel
{
	protected $fillable = [
		'id',
		'name'
	];
}
