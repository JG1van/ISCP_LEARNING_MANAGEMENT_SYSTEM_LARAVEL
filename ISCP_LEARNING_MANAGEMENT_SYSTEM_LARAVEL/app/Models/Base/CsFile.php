<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\CsRoom;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CsFile
 * 
 * @property int $id
 * @property int $room_id
 * @property string $file_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property CsRoom $cs_room
 *
 * @package App\Models\Base
 */
class CsFile extends Model
{
	protected $table = 'cs_files';
	public $incrementing = true;
	protected $casts = [
		'room_id' => 'int'
	];

	public function cs_room()
	{
		return $this->belongsTo(CsRoom::class, 'room_id');
	}
}
