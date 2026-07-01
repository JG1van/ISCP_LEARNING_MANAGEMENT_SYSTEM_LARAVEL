<?php

namespace App\Models;

use App\Models\Base\CsFile as BaseCsFile;

class CsFile extends BaseCsFile
{
	protected $fillable = [
		'room_id',
		'file_path'
	];
}
