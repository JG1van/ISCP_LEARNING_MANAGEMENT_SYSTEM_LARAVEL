<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Exercise;
use App\Models\Serial;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExercisePoint
 * 
 * @property int $id
 * @property int $serial_id
 * @property int $exercise_id
 * @property int $student_id
 * @property string $answer
 * @property string|null $competence_point
 * @property string|null $exercise_point
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Exercise $exercise
 * @property Serial $serial
 * @property Student $student
 *
 * @package App\Models\Base
 */
class ExercisePoint extends Model
{
	protected $table = 'exercise_points';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'serial_id' => 'int',
		'exercise_id' => 'int',
		'student_id' => 'int'
	];

	public function exercise()
	{
		return $this->belongsTo(Exercise::class);
	}

	public function serial()
	{
		return $this->belongsTo(Serial::class);
	}

	public function student()
	{
		return $this->belongsTo(Student::class);
	}
}
