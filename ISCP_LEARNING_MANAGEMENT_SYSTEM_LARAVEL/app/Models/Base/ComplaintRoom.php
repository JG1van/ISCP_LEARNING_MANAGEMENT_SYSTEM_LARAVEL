<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Admin;
use App\Models\ComplaintCategory;
use App\Models\ComplaintMessage;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintRoom
 * 
 * @property int $id
 * @property string $complaint_code
 * @property int $complaint_category_id
 * @property int|null $student_id
 * @property int|null $user_id
 * @property int $admin_id
 * @property string $chat_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Admin $admin
 * @property ComplaintCategory $complaint_category
 * @property Student|null $student
 * @property User|null $user
 * @property Collection|ComplaintMessage[] $complaint_messages
 *
 * @package App\Models\Base
 */
class ComplaintRoom extends Model
{
	protected $table = 'complaint_rooms';

	protected $casts = [
		'complaint_category_id' => 'int',
		'student_id' => 'int',
		'user_id' => 'int',
		'admin_id' => 'int'
	];

	public function admin()
	{
		return $this->belongsTo(Admin::class);
	}

	public function complaint_category()
	{
		return $this->belongsTo(ComplaintCategory::class);
	}

	public function student()
	{
		return $this->belongsTo(Student::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function complaint_messages()
	{
		return $this->hasMany(ComplaintMessage::class);
	}
}
