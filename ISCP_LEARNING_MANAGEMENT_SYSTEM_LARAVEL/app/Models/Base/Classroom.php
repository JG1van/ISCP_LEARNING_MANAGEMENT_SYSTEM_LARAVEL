<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Serial;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
/**
 * Class Classroom
 * 
 * @property int $id
 * @property int $serial_id
 * @property string $name
 * @property string $grade
 * @property string $code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Serial $serial
 * @property Collection|Student[] $students
 *
 * @package App\Models\Base
 */
class Classroom extends Model
{
	protected $table = 'classrooms';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'serial_id' => 'int'
	];



	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	public function serial()
	{
		return $this->belongsTo(Serial::class, 'serial_id');
	}

	public function students()
	{
		return $this->hasMany(Student::class, 'classroom_id');
	}



}
