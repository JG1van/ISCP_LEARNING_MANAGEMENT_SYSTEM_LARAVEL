<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Post;
use App\Models\Serial;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Task
 * 
 * @property int $id
 * @property int $serial_id
 * @property int $post_id
 * @property int $student_id
 * @property string $description
 * @property string|null $attachment
 * @property string|null $point
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Post $post
 * @property Serial $serial
 * @property Student $student
 *
 * @package App\Models\Base
 */
class Task extends Model
{
	protected $table = 'tasks';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'serial_id' => 'int',
		'post_id' => 'int',
		'student_id' => 'int'
	];

	public function post()
	{
		return $this->belongsTo(Post::class);
	}

	public function serial()
	{
		return $this->belongsTo(Serial::class);
	}

	public function student()
	{
		return $this->belongsTo(Student::class);
	}
}
