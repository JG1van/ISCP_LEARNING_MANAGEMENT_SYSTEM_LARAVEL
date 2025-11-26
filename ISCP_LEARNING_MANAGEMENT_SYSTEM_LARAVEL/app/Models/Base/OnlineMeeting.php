<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OnlineMeeting
 * 
 * @property int $id
 * @property int $serial_id
 * @property int $classroom_id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property string $meeting_code
 * @property string $meeting_link
 * @property string|null $platform
 * @property Carbon $start_time
 * @property Carbon|null $end_time
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class OnlineMeeting extends Model
{
	protected $table = 'online_meetings';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'serial_id' => 'int',
		'classroom_id' => 'int',
		'user_id' => 'int',
		'start_time' => 'datetime',
		'end_time' => 'datetime'
	];
}
