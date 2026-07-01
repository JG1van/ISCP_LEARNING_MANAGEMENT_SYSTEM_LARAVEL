<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Classroom;
use App\Models\Exercise;
use App\Models\ExercisePoint;
use App\Models\Post;
use App\Models\Product;
use App\Models\Report;
use App\Models\ShareExercise;
use App\Models\Student;
use App\Models\Task;
use App\Models\User;
use App\Models\SerialLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Serial
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int $product_id
 * @property string $serial
 * @property string $paket
 * @property string $active
 * @property Carbon|null $expired_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Product $product
 * @property User|null $user
 * @property Collection|Classroom[] $classrooms
 * @property Collection|ExercisePoint[] $exercise_points
 * @property Collection|Exercise[] $exercises
 * @property Collection|Post[] $posts
 * @property Collection|Report[] $reports
 * @property Collection|ShareExercise[] $share_exercises
 * @property Collection|Student[] $students
 * @property Collection|Task[] $tasks
 *
 * @package App\Models\Base
 */
class Serial extends Model
{
	protected $table = 'serials';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'user_id' => 'int',
		'product_id' => 'int',
		'expired_at' => 'datetime'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function classrooms()
	{
		return $this->hasMany(Classroom::class);
	}

	public function exercise_points()
	{
		return $this->hasMany(ExercisePoint::class);
	}

	public function exercises()
	{
		return $this->hasMany(Exercise::class);
	}

	public function posts()
	{
		return $this->hasMany(Post::class);
	}

	public function reports()
	{
		return $this->hasMany(Report::class);
	}

	public function share_exercises()
	{
		return $this->hasMany(ShareExercise::class);
	}

	public function students()
	{
		return $this->hasMany(Student::class);
	}

	public function tasks()
	{
		return $this->hasMany(Task::class);
	}
	public function logs()
	{
		return $this->hasMany(SerialLog::class, 'serial_id');
	}

}
