<?php

namespace App\Models;

use App\Models\Base\CsMessage as BaseCsMessage;

class CsMessage extends BaseCsMessage
{
	protected $fillable = [
		'cs_rooms_id',
		'message_sender',
		'message_content',
		'sent_time'
	];
}
