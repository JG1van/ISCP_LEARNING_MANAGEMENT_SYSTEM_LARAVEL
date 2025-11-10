<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ComplaintMessagesStudent;
use App\Models\ComplaintStudentFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintStudentDetail
 * 
 * @property int $id
 * @property int $complaint_message_student_id
 * @property string $message_sender
 * @property string $message_content
 * @property Carbon $sent_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ComplaintMessagesStudent $complaint_messages_student
 * @property Collection|ComplaintStudentFile[] $complaint_student_files
 *
 * @package App\Models\Base
 */
class ComplaintStudentDetail extends Model
{
	protected $table = 'complaint_student_details';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'complaint_message_student_id' => 'int',
		'sent_time' => 'datetime'
	];

	public function complaint_messages_student()
	{
		return $this->belongsTo(ComplaintMessagesStudent::class, 'complaint_message_student_id');
	}

	public function complaint_student_files()
	{
		return $this->hasMany(ComplaintStudentFile::class);
	}
}
