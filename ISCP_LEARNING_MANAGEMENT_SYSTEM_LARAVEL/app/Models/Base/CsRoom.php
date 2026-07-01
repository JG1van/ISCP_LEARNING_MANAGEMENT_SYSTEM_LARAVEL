<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Admin;
use App\Models\CsFile;
use App\Models\CsMessage;
use App\Models\QuestionCategory;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CsRoom
 * 
 * @property int $id
 * @property string $room_code
 * @property int|null $question_categories_id
 * @property int|null $student_id
 * @property int|null $user_id
 * @property int|null $admin_id
 * @property string $chat_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Admin|null $admin
 * @property QuestionCategory|null $question_category
 * @property Student|null $student
 * @property User|null $user
 * @property Collection|CsFile[] $cs_files
 * @property Collection|CsMessage[] $cs_messages
 *
 * @package App\Models\Base
 */
class CsRoom extends Model
{
	protected $table = 'cs_rooms';
	public $incrementing = true;
	protected $casts = [
		'question_categories_id' => 'int',
		'student_id' => 'int',
		'user_id' => 'int',
		'admin_id' => 'int'
	];

	public function admin()
	{
		return $this->belongsTo(Admin::class);
	}

	public function question_category()
	{
		return $this->belongsTo(QuestionCategory::class, 'question_categories_id');
	}

	public function student()
	{
		return $this->belongsTo(Student::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function cs_files()
	{
		return $this->hasMany(CsFile::class, 'room_id');
	}

	public function cs_messages()
	{
		return $this->hasMany(CsMessage::class, 'cs_rooms_id');
	}
}
