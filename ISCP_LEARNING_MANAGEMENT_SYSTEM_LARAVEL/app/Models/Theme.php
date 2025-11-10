<?php

namespace App\Models;

use App\Models\Base\Theme as BaseTheme;

class Theme extends BaseTheme
{
	protected $fillable = [
		'id',
		'lesson_id',
		'theme',
		'name'
	];
}
