<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\CsLog;
use App\Models\CsRoom;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class QuestionCategory
 * 
 * @property int $id
 * @property string $name
 * @property string $level
 * @property string|null $solution_text
 * @property string|null $guide_file
 * @property string|null $guide_video
 * @property string $category_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|CsLog[] $cs_logs
 * @property Collection|CsRoom[] $cs_rooms
 *
 * @package App\Models\Base
 */
class QuestionCategory extends Model
{
	protected $table = 'question_categories';
	public $incrementing = true;
	public function cs_logs()
	{
		return $this->hasMany(CsLog::class, 'question_categories_id');
	}

	public function cs_rooms()
	{
		return $this->hasMany(CsRoom::class, 'question_categories_id');
	}
}
