<?php

namespace App\Models;

use App\Models\Base\Subtheme as BaseSubtheme;

class Subtheme extends BaseSubtheme
{
	protected $fillable = [
		'id',
		'lesson_id',
		'theme_id',
		'subtheme',
		'name'
	];
}
