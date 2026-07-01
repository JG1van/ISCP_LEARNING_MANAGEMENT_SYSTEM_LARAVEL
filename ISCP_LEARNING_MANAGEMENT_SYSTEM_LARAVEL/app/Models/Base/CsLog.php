<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Admin;
use App\Models\QuestionCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CsLog
 * 
 * @property int $id
 * @property int|null $question_categories_id
 * @property int|null $admin_id
 * @property Carbon $completion_time
 * @property string $resolution_by
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Admin|null $admin
 * @property QuestionCategory|null $question_category
 *
 * @package App\Models\Base
 */
class CsLog extends Model
{
	protected $table = 'cs_logs';
	public $incrementing = true;
	protected $casts = [
		'question_categories_id' => 'int',
		'admin_id' => 'int',
		'completion_time' => 'datetime'
	];

	public function admin()
	{
		return $this->belongsTo(Admin::class);
	}

	public function question_category()
	{
		return $this->belongsTo(QuestionCategory::class, 'question_categories_id');
	}
}
