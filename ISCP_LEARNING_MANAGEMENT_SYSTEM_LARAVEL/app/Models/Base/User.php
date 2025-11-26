<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ComplaintHistoryUser;
use App\Models\ComplaintMessagesUser;
use App\Models\ExerciseItem;
use App\Models\Post;
use App\Models\PostChildComment;
use App\Models\PostComment;
use App\Models\Serial;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string|null $email
 * @property int $role
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $img
 * @property Carbon|null $login_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at

 * @property Collection|ExerciseItem[] $exercise_items
 * @property Collection|PostChildComment[] $post_child_comments
 * @property Collection|PostComment[] $post_comments
 * @property Collection|Post[] $posts
 * @property Collection|Serial[] $serials
 * @property Collection|Student[] $students
 *
 * @package App\Models\Base
 */
class User extends Model
{
	protected $table = 'users';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'role' => 'int',
		'login_at' => 'datetime',

	];





	public function exercise_items()
	{
		return $this->hasMany(ExerciseItem::class);
	}

	public function post_child_comments()
	{
		return $this->hasMany(PostChildComment::class);
	}

	public function post_comments()
	{
		return $this->hasMany(PostComment::class);
	}

	public function posts()
	{
		return $this->hasMany(Post::class);
	}

	public function serials()
	{
		return $this->hasMany(Serial::class);
	}

	public function students()
	{
		return $this->hasMany(Student::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

}
