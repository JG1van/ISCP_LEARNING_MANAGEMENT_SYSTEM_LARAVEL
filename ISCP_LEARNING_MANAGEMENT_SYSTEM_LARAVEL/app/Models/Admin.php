<?php

namespace App\Models;

use App\Models\Base\Admin as BaseAdmin;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Admin extends BaseAdmin implements AuthenticatableContract
{
	use Authenticatable;

	protected $hidden = [
		'password',
	];

	protected $fillable = [
		'id',
		'name',
		'username',
		'password',
		'role',
		'date_in',
		'position',
		'phone',
		'img',
		'login_at',
	];

	/**
	 * Gunakan 'username' sebagai field login, bukan 'email'
	 */

}
