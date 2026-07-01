<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\CsRoom;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CsMessage
 * 
 * @property int $id
 * @property int $cs_rooms_id
 * @property string $message_sender
 * @property string|null $message_content
 * @property Carbon|null $sent_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property CsRoom $cs_room
 *
 * @package App\Models\Base
 */
class CsMessage extends Model
{
	protected $table = 'cs_messages';
	public $incrementing = true;
	protected $casts = [
		'cs_rooms_id' => 'int',
		'sent_time' => 'datetime'
	];

	public function cs_room()
	{
		return $this->belongsTo(CsRoom::class, 'cs_rooms_id');
	}
}
