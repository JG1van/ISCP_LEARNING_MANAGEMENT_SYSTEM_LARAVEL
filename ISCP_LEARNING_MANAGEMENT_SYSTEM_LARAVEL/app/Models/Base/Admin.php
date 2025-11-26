<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ComplaintHistoryStudent;
use App\Models\ComplaintHistoryUser;
use App\Models\ComplaintMessagesStudent;
use App\Models\ComplaintMessagesUser;
use App\Models\ExerciseItem;
use App\Models\LessonItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class Admin
 * 
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property int $role
 * @property string|null $date_in
 * @property string|null $position
 * @property string|null $phone
 * @property string|null $img
 * @property Carbon|null $login_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ComplaintHistoryStudent[] $complaint_history_students
 * @property Collection|ComplaintHistoryUser[] $complaint_history_users
 * @property Collection|ComplaintMessagesStudent[] $complaint_messages_students
 * @property Collection|ComplaintMessagesUser[] $complaint_messages_users
 * @property Collection|ExerciseItem[] $exercise_items
 * @property Collection|LessonItem[] $lesson_items
 *
 * @package App\Models\Base
 */
class Admin extends Authenticatable
{
	use Notifiable;

	protected $table = 'admins';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'role' => 'int',
		'login_at' => 'datetime',
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

	public function exercise_items()
	{
		return $this->hasMany(ExerciseItem::class);
	}

	public function lesson_items()
	{
		return $this->hasMany(LessonItem::class);
	}
}
