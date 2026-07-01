<?php

namespace App\Models;

use App\Models\Base\SerialLog as BaseSerialLog;

class SerialLog extends BaseSerialLog
{
	protected $fillable = [
		'serial_id',
		'active',
		'status'
	];
}
