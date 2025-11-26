<?php

namespace App\Models;

use App\Models\Base\Serial as BaseSerial;

class Serial extends BaseSerial
{
	protected $fillable = [

		'user_id',
		'product_id',
		'serial',
		'paket',
		'active',
		'expired_at'
	];
}
