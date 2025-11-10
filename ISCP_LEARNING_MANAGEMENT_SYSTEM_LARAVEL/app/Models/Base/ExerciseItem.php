<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Admin;
use App\Models\Competence;
use App\Models\Exercise;
use App\Models\ExerciseModel;
use App\Models\ExerciseType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExerciseItem
 * 
 * @property int $id
 * @property int|null $admin_id
 * @property int|null $user_id
 * @property int|null $competence_id
 * @property int $exercise_id
 * @property int $exercise_type_id
 * @property int $exercise_model_id
 * @property int $exercise_choice
 * @property int $exercise_number
 * @property string $question
 * @property string|null $selection
 * @property string|null $answer
 * @property int $is_user
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Admin|null $admin
 * @property Competence|null $competence
 * @property Exercise $exercise
 * @property ExerciseModel $exercise_model
 * @property ExerciseType $exercise_type
 * @property User|null $user
 *
 * @package App\Models\Base
 */
class ExerciseItem extends Model
{
	protected $table = 'exercise_items';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'admin_id' => 'int',
		'user_id' => 'int',
		'competence_id' => 'int',
		'exercise_id' => 'int',
		'exercise_type_id' => 'int',
		'exercise_model_id' => 'int',
		'exercise_choice' => 'int',
		'exercise_number' => 'int',
		'is_user' => 'int'
	];

	public function admin()
	{
		return $this->belongsTo(Admin::class);
	}

	public function competence()
	{
		return $this->belongsTo(Competence::class);
	}

	public function exercise()
	{
		return $this->belongsTo(Exercise::class);
	}

	public function exercise_model()
	{
		return $this->belongsTo(ExerciseModel::class);
	}

	public function exercise_type()
	{
		return $this->belongsTo(ExerciseType::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
