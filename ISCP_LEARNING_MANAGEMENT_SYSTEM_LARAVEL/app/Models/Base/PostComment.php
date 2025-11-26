<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Post;
use App\Models\PostChildComment;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PostComment
 * 
 * @property int $id
 * @property int $post_id
 * @property int|null $user_id
 * @property int|null $student_id
 * @property string $message
 * @property string $code
 * @property bool $is_user
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Post $post
 * @property Student|null $student
 * @property User|null $user
 * @property Collection|PostChildComment[] $post_child_comments
 *
 * @package App\Models\Base
 */
class PostComment extends Model
{
	protected $table = 'post_comments';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'post_id' => 'int',
		'user_id' => 'int',
		'student_id' => 'int',
		'is_user' => 'bool'
	];

	public function post()
	{
		return $this->belongsTo(Post::class);
	}

	public function student()
	{
		return $this->belongsTo(Student::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function post_child_comments()
	{
		return $this->hasMany(PostChildComment::class);
	}
}
