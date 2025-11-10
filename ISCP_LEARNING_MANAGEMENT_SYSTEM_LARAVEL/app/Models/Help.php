<?php

namespace App\Models;

use App\Models\Base\Help as BaseHelp;

class Help extends BaseHelp
{
	protected $fillable = [
		'title',
		'description',
		'priority'
	];
}
