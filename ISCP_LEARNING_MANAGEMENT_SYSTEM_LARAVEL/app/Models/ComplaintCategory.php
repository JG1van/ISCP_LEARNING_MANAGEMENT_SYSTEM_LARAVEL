<?php

namespace App\Models;

use App\Models\Base\ComplaintCategory as BaseComplaintCategory;

class ComplaintCategory extends BaseComplaintCategory
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
