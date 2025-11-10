<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ExerciseItem;
use App\Models\Lesson;
use App\Models\Mapel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Competence
 * 
 * @property int $id
 * @property int $lesson_id
 * @property int $mapel_id
 * @property string $point
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Lesson $lesson
 * @property Mapel $mapel
 * @property Collection|ExerciseItem[] $exercise_items
 *
 * @package App\Models\Base
 */
class Competence extends Model
{
	protected $table = 'competences';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'lesson_id' => 'int',
		'mapel_id' => 'int'
	];

	public function lesson()
	{
		return $this->belongsTo(Lesson::class);
	}

	public function mapel()
	{
		return $this->belongsTo(Mapel::class);
	}

	public function exercise_items()
	{
		return $this->hasMany(ExerciseItem::class);
	}
}
