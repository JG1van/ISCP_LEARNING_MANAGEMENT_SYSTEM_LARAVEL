<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Help
 * 
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Help extends Model
{
	protected $table = 'helps';
	public $incrementing = true;

	protected $casts = [
		'id' => 'int',
		'priority' => 'int'
	];
}
