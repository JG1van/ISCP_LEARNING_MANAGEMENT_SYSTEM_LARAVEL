<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Admin;
use App\Models\ComplaintUserDetail;
use App\Models\IssueCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintMessagesUser
 * 
 * @property int $id
 * @property int $reporter_user_id
 * @property int|null $admin_id
 * @property int $issue_category_id
 * @property Carbon $report_time
 * @property string $chat_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Admin|null $admin
 * @property IssueCategory $issue_category
 * @property User $user
 * @property Collection|ComplaintUserDetail[] $complaint_user_details
 *
 * @package App\Models\Base
 */
class ComplaintMessagesUser extends Model
{
	protected $table = 'complaint_messages_users';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'reporter_user_id' => 'int',
		'admin_id' => 'int',
		'issue_category_id' => 'int',
		'report_time' => 'datetime'
	];

	public function admin()
	{
		return $this->belongsTo(Admin::class);
	}

	public function issue_category()
	{
		return $this->belongsTo(IssueCategory::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'reporter_user_id');
	}

	public function complaint_user_details()
	{
		return $this->hasMany(ComplaintUserDetail::class, 'complaint_message_user_id');
	}
}
