<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Exercise;
use App\Models\Serial;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShareExercise
 * 
 * @property int $serial_id
 * @property int $exercise_id
 * 
 * @property Exercise $exercise
 * @property Serial $serial
 *
 * @package App\Models\Base
 */
class ShareExercise extends Model
{
	protected $table = 'share_exercises';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'serial_id' => 'int',
		'exercise_id' => 'int'
	];

	public function exercise()
	{
		return $this->belongsTo(Exercise::class);
	}

	public function serial()
	{
		return $this->belongsTo(Serial::class);
	}
}
