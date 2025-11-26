<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Competence;
use App\Models\Exercise;
use App\Models\LessonItem;
use App\Models\Mapel;
use App\Models\Subtheme;
use App\Models\Theme;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lesson
 * 
 * @property int $id
 * @property int $mapel_id
 * @property string $name
 * @property string $grade
 * @property int $semester
 * @property int $category
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Mapel $mapel
 * @property Collection|Competence[] $competences
 * @property Collection|Exercise[] $exercises
 * @property Collection|LessonItem[] $lesson_items
 * @property Collection|Subtheme[] $subthemes
 * @property Collection|Theme[] $themes
 *
 * @package App\Models\Base
 */
class Lesson extends Model
{
	protected $table = 'lessons';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'mapel_id' => 'int',
		'semester' => 'int',
		'category' => 'int'
	];

	public function mapel()
	{
		return $this->belongsTo(Mapel::class);
	}

	public function competences()
	{
		return $this->hasMany(Competence::class);
	}

	public function exercises()
	{
		return $this->hasMany(Exercise::class);
	}

	public function lesson_items()
	{
		return $this->hasMany(LessonItem::class);
	}

	public function subthemes()
	{
		return $this->hasMany(Subtheme::class);
	}

	public function themes()
	{
		return $this->hasMany(Theme::class);
	}
}
