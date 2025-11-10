<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ComplaintStudentDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintStudentFile
 * 
 * @property int $id
 * @property int $complaint_student_detail_id
 * @property string $complaint_file
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ComplaintStudentDetail $complaint_student_detail
 *
 * @package App\Models\Base
 */
class ComplaintStudentFile extends Model
{
	protected $table = 'complaint_student_files';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'complaint_student_detail_id' => 'int'
	];

	public function complaint_student_detail()
	{
		return $this->belongsTo(ComplaintStudentDetail::class);
	}
}
