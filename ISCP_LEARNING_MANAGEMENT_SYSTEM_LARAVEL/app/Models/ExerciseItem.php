<?php

namespace App\Models;

use App\Models\Base\ExerciseItem as BaseExerciseItem;

class ExerciseItem extends BaseExerciseItem
{
	protected $fillable = [

		'admin_id',
		'user_id',
		'competence_id',
		'exercise_id',
		'exercise_type_id',
		'exercise_model_id',
		'exercise_choice',
		'exercise_number',
		'question',
		'selection',
		'answer',
		'is_user'
	];
}
