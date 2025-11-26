<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Serial;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property int $id
 * @property string|null $lesson_id
 * @property string $name
 * @property string|null $grade
 * @property string $grade_category
 * @property string|null $semester
 * 
 * @property Collection|Serial[] $serials
 *
 * @package App\Models\Base
 */
class Product extends Model
{
	protected $table = 'products';
	public $incrementing = true;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	public function serials()
	{
		return $this->hasMany(Serial::class);
	}
}
