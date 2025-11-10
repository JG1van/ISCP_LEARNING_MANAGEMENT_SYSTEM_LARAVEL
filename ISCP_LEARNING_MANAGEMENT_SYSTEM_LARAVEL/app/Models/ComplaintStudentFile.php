<?php

namespace App\Models;

use App\Models\Base\ComplaintStudentFile as BaseComplaintStudentFile;

class ComplaintStudentFile extends BaseComplaintStudentFile
{
	protected $fillable = [
		'complaint_student_detail_id',
		'complaint_file'
	];
}
