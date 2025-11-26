<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\PostComment;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PostChildComment
 * 
 * @property int $id
 * @property int $post_comment_id
 * @property int|null $user_id
 * @property int|null $student_id
 * @property string $message
 * @property bool $is_user
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property PostComment $post_comment
 * @property Student|null $student
 * @property User|null $user
 *
 * @package App\Models\Base
 */
class PostChildComment extends Model
{
	protected $table = 'post_child_comments';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'post_comment_id' => 'int',
		'user_id' => 'int',
		'student_id' => 'int',
		'is_user' => 'bool'
	];

	public function post_comment()
	{
		return $this->belongsTo(PostComment::class);
	}

	public function student()
	{
		return $this->belongsTo(Student::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
