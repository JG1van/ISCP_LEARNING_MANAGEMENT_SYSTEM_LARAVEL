<?php

namespace App\Models;

use App\Models\Base\PostChildComment as BasePostChildComment;

class PostChildComment extends BasePostChildComment
{
	protected $fillable = [
		'post_comment_id',
		'user_id',
		'student_id',
		'message',
		'is_user'
	];
}
