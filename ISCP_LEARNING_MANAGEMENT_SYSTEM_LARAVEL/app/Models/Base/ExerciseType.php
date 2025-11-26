<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Exercise;
use App\Models\ExerciseItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExerciseType
 * 
 * @property int $id
 * @property string $kode
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ExerciseItem[] $exercise_items
 * @property Collection|Exercise[] $exercises
 *
 * @package App\Models\Base
 */
class ExerciseType extends Model
{
	protected $table = 'exercise_types';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int'
	];

	public function exercise_items()
	{
		return $this->hasMany(ExerciseItem::class);
	}

	public function exercises()
	{
		return $this->hasMany(Exercise::class);
	}
	public function type()
	{
		return $this->belongsTo(ExerciseType::class, 'exercise_type_id');
	}

}
