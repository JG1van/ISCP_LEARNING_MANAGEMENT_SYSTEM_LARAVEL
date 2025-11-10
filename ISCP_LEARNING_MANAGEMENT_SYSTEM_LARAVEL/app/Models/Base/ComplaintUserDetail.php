<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ComplaintMessagesUser;
use App\Models\ComplaintUserFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintUserDetail
 * 
 * @property int $id
 * @property int $complaint_message_user_id
 * @property string $message_sender
 * @property string $message_content
 * @property Carbon $sent_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ComplaintMessagesUser $complaint_messages_user
 * @property Collection|ComplaintUserFile[] $complaint_user_files
 *
 * @package App\Models\Base
 */
class ComplaintUserDetail extends Model
{
	protected $table = 'complaint_user_details';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'complaint_message_user_id' => 'int',
		'sent_time' => 'datetime'
	];

	public function complaint_messages_user()
	{
		return $this->belongsTo(ComplaintMessagesUser::class, 'complaint_message_user_id');
	}

	public function complaint_user_files()
	{
		return $this->hasMany(ComplaintUserFile::class);
	}
}
