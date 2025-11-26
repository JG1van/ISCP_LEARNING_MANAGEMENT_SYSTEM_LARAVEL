<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Admin;
use App\Models\Lesson;
use App\Models\Subtheme;
use App\Models\Theme;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LessonItem
 * 
 * @property int $id
 * @property int $lesson_id
 * @property int $theme_id
 * @property int $subtheme_id
 * @property int $admin_id
 * @property int $number
 * @property string $title
 * @property string $embed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Admin $admin
 * @property Lesson $lesson
 * @property Subtheme $subtheme
 * @property Theme $theme
 *
 * @package App\Models\Base
 */
class LessonItem extends Model
{
	protected $table = 'lesson_items';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'lesson_id' => 'int',
		'theme_id' => 'int',
		'subtheme_id' => 'int',
		'admin_id' => 'int',
		'number' => 'int'
	];

	public function admin()
	{
		return $this->belongsTo(Admin::class);
	}

	public function lesson()
	{
		return $this->belongsTo(Lesson::class);
	}

	public function subtheme()
	{
		return $this->belongsTo(Subtheme::class);
	}

	public function theme()
	{
		return $this->belongsTo(Theme::class);
	}

}
