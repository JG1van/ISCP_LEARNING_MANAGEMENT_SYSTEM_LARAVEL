<?php

namespace App\Models;

use App\Models\Base\Student as BaseStudent;

class Student extends BaseStudent
{
	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'id',
		'serial_id',
		'user_id',
		'classroom_id',
		'name',
		'username',
		'password',
		'password_text',
		'nis',
		'email',
		'phone',

	];
}
