<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Subtheme;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Theme
 * 
 * @property int $id
 * @property int $lesson_id
 * @property int $theme
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Lesson $lesson
 * @property Collection|LessonItem[] $lesson_items
 * @property Collection|Subtheme[] $subthemes
 *
 * @package App\Models\Base
 */
class Theme extends Model
{
	protected $table = 'themes';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'lesson_id' => 'int',
		'theme' => 'int'
	];

	public function lesson()
	{
		return $this->belongsTo(Lesson::class);
	}

	public function lesson_items()
	{
		return $this->hasMany(LessonItem::class);
	}

	public function subthemes()
	{
		return $this->hasMany(Subtheme::class);
	}
}
