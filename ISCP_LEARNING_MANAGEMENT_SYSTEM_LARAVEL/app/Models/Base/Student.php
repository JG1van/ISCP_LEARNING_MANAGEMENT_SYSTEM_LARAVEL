<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Classroom;
use App\Models\ExercisePoint;
use App\Models\PostChildComment;
use App\Models\PostComment;
use App\Models\Report;
use App\Models\Serial;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Student
 * 
 * @property int $id
 * @property int $serial_id
 * @property int $user_id
 * @property int $classroom_id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $password_text
 * @property string|null $nis
 * @property string|null $email
 * @property string|null $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Classroom $classroom
 * @property Serial $serial
 * @property User $user
 * @property Collection|ExercisePoint[] $exercise_points
 * @property Collection|PostChildComment[] $post_child_comments
 * @property Collection|PostComment[] $post_comments
 * @property Collection|Report[] $reports
 * @property Collection|Task[] $tasks
 *
 * @package App\Models\Base
 */
class Student extends Model
{
	protected $table = 'students';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'serial_id' => 'int',
		'user_id' => 'int',
		'classroom_id' => 'int'
	];

	public function classroom()
	{
		return $this->belongsTo(Classroom::class);
	}

	public function serial()
	{
		return $this->belongsTo(Serial::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}


	public function exercise_points()
	{
		return $this->hasMany(ExercisePoint::class);
	}

	public function post_child_comments()
	{
		return $this->hasMany(PostChildComment::class);
	}

	public function post_comments()
	{
		return $this->hasMany(PostComment::class);
	}

	public function reports()
	{
		return $this->hasMany(Report::class);
	}

	public function tasks()
	{
		return $this->hasMany(Task::class);
	}
}
