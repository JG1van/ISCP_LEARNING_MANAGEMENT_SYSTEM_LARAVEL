<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ExerciseItem;
use App\Models\ExercisePoint;
use App\Models\ExerciseType;
use App\Models\Lesson;
use App\Models\Serial;
use App\Models\ShareExercise;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Exercise
 * 
 * @property int $id
 * @property int $lesson_id
 * @property int|null $serial_id
 * @property int $exercise_type_id
 * @property string|null $title
 * @property int $is_admin
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ExerciseType $exercise_type
 * @property Lesson $lesson
 * @property Serial|null $serial
 * @property Collection|ExerciseItem[] $exercise_items
 * @property Collection|ExercisePoint[] $exercise_points
 * @property Collection|ShareExercise[] $share_exercises
 *
 * @package App\Models\Base
 */
class Exercise extends Model
{
	protected $table = 'exercises';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'lesson_id' => 'int',
		'serial_id' => 'int',
		'exercise_type_id' => 'int',
		'is_admin' => 'int'
	];

	public function exercise_type()
	{
		return $this->belongsTo(ExerciseType::class);
	}

	public function lesson()
	{
		return $this->belongsTo(Lesson::class);
	}

	public function serial()
	{
		return $this->belongsTo(Serial::class);
	}

	public function exercise_items()
	{
		return $this->hasMany(ExerciseItem::class);
	}

	public function exercise_points()
	{
		return $this->hasMany(ExercisePoint::class);
	}

	public function share_exercises()
	{
		return $this->hasMany(ShareExercise::class);
	}

	public function type()
	{
		return $this->belongsTo(ExerciseType::class, 'exercise_type_id');
	}

}
