<?php

namespace App\Models;

use App\Models\Base\ComplaintUserFile as BaseComplaintUserFile;

class ComplaintUserFile extends BaseComplaintUserFile
{
	protected $fillable = [
		'complaint_user_detail_id',
		'complaint_file'
	];
}
