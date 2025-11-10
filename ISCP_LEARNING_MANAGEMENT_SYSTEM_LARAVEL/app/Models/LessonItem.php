<?php

namespace App\Models;

use App\Models\Base\LessonItem as BaseLessonItem;

class LessonItem extends BaseLessonItem
{
	protected $fillable = [
		'id',
		'lesson_id',
		'theme_id',
		'subtheme_id',
		'admin_id',
		'number',
		'title',
		'embed'
	];
}
