<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Serial;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SerialLog
 * 
 * @property int $id
 * @property int $serial_id
 * @property string $active
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Serial $serial
 *
 * @package App\Models\Base
 */
class SerialLog extends Model
{
	protected $table = 'serial_logs';
	public $incrementing = true;

	protected $casts = [
		'serial_id' => 'int'
	];

	public function serial()
	{
		return $this->belongsTo(Serial::class);
	}
}
