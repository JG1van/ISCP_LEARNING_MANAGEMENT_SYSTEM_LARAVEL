<?php

namespace App\Models;

use App\Models\Base\PostComment as BasePostComment;

class PostComment extends BasePostComment
{
	protected $fillable = [
		'post_id',
		'user_id',
		'student_id',
		'message',
		'code',
		'is_user'
	];
}
