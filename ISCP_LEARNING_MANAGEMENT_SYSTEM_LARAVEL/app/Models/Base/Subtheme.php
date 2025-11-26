<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subtheme
 * 
 * @property int $id
 * @property int $lesson_id
 * @property int $theme_id
 * @property int $subtheme
 * @property string $name
 * 
 * @property Lesson $lesson
 * @property Theme $theme
 * @property Collection|LessonItem[] $lesson_items
 *
 * @package App\Models\Base
 */
class Subtheme extends Model
{
	protected $table = 'subthemes';
	public $incrementing = true;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'lesson_id' => 'int',
		'theme_id' => 'int',
		'subtheme' => 'int'
	];

	public function lesson()
	{
		return $this->belongsTo(Lesson::class);
	}

	public function theme()
	{
		return $this->belongsTo(Theme::class);
	}

	public function lesson_items()
	{
		return $this->hasMany(LessonItem::class);
	}
	public function lessonItems()
	{
		return $this->hasMany(LessonItem::class, 'subtheme_id');
	}
}
