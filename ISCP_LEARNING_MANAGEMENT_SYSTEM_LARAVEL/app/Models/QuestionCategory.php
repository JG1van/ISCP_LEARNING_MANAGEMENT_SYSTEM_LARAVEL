<?php

namespace App\Models;

use App\Models\Base\QuestionCategory as BaseQuestionCategory;

class QuestionCategory extends BaseQuestionCategory
{
	protected $fillable = [
		'name',
		'level',
		'solution_text',
		'guide_file',
		'guide_video',
		'category_status'
	];
}
