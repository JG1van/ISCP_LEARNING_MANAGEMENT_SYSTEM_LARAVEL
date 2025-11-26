<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ComplaintLog;
use App\Models\ComplaintRoom;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplaintCategory
 * 
 * @property int $id
 * @property string $name
 * @property string $level
 * @property string|null $solution_text
 * @property string|null $guide_file
 * @property string|null $guide_video
 * @property string $category_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ComplaintLog[] $complaint_logs
 * @property Collection|ComplaintRoom[] $complaint_rooms
 *
 * @package App\Models\Base
 */
class ComplaintCategory extends Model
{
	protected $table = 'complaint_categories';

	public function complaint_logs()
	{
		return $this->hasMany(ComplaintLog::class);
	}

	public function complaint_rooms()
	{
		return $this->hasMany(ComplaintRoom::class);
	}
}
