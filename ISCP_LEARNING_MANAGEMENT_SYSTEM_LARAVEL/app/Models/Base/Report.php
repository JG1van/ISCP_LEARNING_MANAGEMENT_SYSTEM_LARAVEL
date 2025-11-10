<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Serial;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Report
 * 
 * @property int $id
 * @property int $serial_id
 * @property int $student_id
 * @property string $report
 * @property string|null $img
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Serial $serial
 * @property Student $student
 *
 * @package App\Models\Base
 */
class Report extends Model
{
	protected $table = 'reports';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'serial_id' => 'int',
		'student_id' => 'int'
	];

	public function serial()
	{
		return $this->belongsTo(Serial::class);
	}

	public function student()
	{
		return $this->belongsTo(Student::class);
	}
}
