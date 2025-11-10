<?php

namespace App\Models;

use App\Models\Base\Competence as BaseCompetence;

class Competence extends BaseCompetence
{
	protected $fillable = [
		'id',
		'lesson_id',
		'mapel_id',
		'point',
		'description'
	];
}
