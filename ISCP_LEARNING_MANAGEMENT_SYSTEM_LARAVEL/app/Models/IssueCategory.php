<?php

namespace App\Models;

use App\Models\Base\IssueCategory as BaseIssueCategory;

class IssueCategory extends BaseIssueCategory
{
	protected $fillable = [
		'issue_category_name',
		'level',
		'solution_text',
		'guide_file',
		'guide_video',
		'issue_category_status'
	];
}
