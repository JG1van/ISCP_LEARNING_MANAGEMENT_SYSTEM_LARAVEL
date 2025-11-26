<?php

namespace App\Models;

use App\Models\Base\User as BaseUser;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class User extends BaseUser implements AuthenticatableContract
{
	use Authenticatable;

	public $role = 'guru';

	protected $hidden = [
		'password',
	];

	protected $fillable = [
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
