<?php

namespace App\Models;

use App\Models\Base\Lesson as BaseLesson;

class Lesson extends BaseLesson
{
	protected $fillable = [
		'id',
		'mapel_id',
		'name',
		'grade',
		'semester',
		'category'
	];
}
