<?php

namespace App\Models;

use App\Models\Base\Student as BaseStudent;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
class Student extends BaseStudent implements AuthenticatableContract
{
	use Authenticatable;
	public $role = 'siswa';

	protected $hidden = [
		'password'
	];

	protected $fillable = [

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
