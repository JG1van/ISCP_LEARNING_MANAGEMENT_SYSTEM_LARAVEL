<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ComplaintRoom;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintMessage
 * 
 * @property int $id
 * @property int $complaint_room_id
 * @property string $message_sender
 * @property string|null $message_content
 * @property Carbon|null $sent_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ComplaintRoom $complaint_room

 *
 * @package App\Models\Base
 */
class ComplaintMessage extends Model
{
	protected $table = 'complaint_messages';

	protected $casts = [
		'complaint_room_id' => 'int',
		'sent_time' => 'datetime'
	];

	public function complaint_room()
	{
		return $this->belongsTo(ComplaintRoom::class);
	}




}
