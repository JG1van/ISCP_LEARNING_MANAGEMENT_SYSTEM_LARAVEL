<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Admin;
use App\Models\ComplaintCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintLog
 * 
 * @property int $id
 * @property int $complaint_category_id
 * @property int $admin_id
 * @property Carbon $completion_time
 * @property string $resolution_by
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Admin $admin
 * @property ComplaintCategory $complaint_category
 *
 * @package App\Models\Base
 */
class ComplaintLog extends Model
{
	protected $table = 'complaint_logs';

	protected $casts = [
		'complaint_category_id' => 'int',
		'admin_id' => 'int',
		'completion_time' => 'datetime'
	];

	public function admin()
	{
		return $this->belongsTo(Admin::class);
	}

	public function complaint_category()
	{
		return $this->belongsTo(ComplaintCategory::class);
	}
}
