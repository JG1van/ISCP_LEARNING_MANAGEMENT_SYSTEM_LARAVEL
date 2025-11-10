<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ExerciseItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExerciseModel
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ExerciseItem[] $exercise_items
 *
 * @package App\Models\Base
 */
class ExerciseModel extends Model
{
	protected $table = 'exercise_models';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int'
	];

	public function exercise_items()
	{
		return $this->hasMany(ExerciseItem::class);
	}
}
