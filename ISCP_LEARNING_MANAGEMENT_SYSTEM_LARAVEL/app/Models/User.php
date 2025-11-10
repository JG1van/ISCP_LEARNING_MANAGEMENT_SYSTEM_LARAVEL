<?php

namespace App\Models;

use App\Models\Base\User as BaseUser;

class User extends BaseUser
{
	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'id',
		'name',
		'username',
		'password',
		'email',
		'role',
		'address',
		'phone',
		'img',
		'login_at',

	];
}
