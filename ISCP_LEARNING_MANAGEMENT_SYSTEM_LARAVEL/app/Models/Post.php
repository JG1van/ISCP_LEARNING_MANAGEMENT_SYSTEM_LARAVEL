<?php

namespace App\Models;

use App\Models\Base\Post as BasePost;

class Post extends BasePost
{
	protected $fillable = [
		'serial_id',
		'user_id',
		'mapel_id',
		'title',
		'description',
		'slug',
		'link',
		'attachment',
		'embed',
		'category',
		'is_task'
	];
}
