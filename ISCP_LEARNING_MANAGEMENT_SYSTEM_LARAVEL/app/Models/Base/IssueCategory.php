<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ComplaintHistoryStudent;
use App\Models\ComplaintHistoryUser;
use App\Models\ComplaintMessagesStudent;
use App\Models\ComplaintMessagesUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class IssueCategory
 * 
 * @property int $id
 * @property string $issue_category_name
 * @property string $level
 * @property string $solution_text
 * @property string|null $guide_file
 * @property string|null $guide_video
 * @property string $issue_category_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ComplaintHistoryStudent[] $complaint_history_students
 * @property Collection|ComplaintHistoryUser[] $complaint_history_users
 * @property Collection|ComplaintMessagesStudent[] $complaint_messages_students
 * @property Collection|ComplaintMessagesUser[] $complaint_messages_users
 *
 * @package App\Models\Base
 */
class IssueCategory extends Model
{
	protected $table = 'issue_categories';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int'
	];

	public function complaint_history_students()
	{
		return $this->hasMany(ComplaintHistoryStudent::class);
	}

	public function complaint_history_users()
	{
		return $this->hasMany(ComplaintHistoryUser::class);
	}

	public function complaint_messages_students()
	{
		return $this->hasMany(ComplaintMessagesStudent::class);
	}

	public function complaint_messages_users()
	{
		return $this->hasMany(ComplaintMessagesUser::class);
	}
}
