<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ComplaintUserDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintUserFile
 * 
 * @property int $id
 * @property int $complaint_user_detail_id
 * @property string $complaint_file
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ComplaintUserDetail $complaint_user_detail
 *
 * @package App\Models\Base
 */
class ComplaintUserFile extends Model
{
	protected $table = 'complaint_user_files';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'complaint_user_detail_id' => 'int'
	];

	public function complaint_user_detail()
	{
		return $this->belongsTo(ComplaintUserDetail::class);
	}
}
