<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Competence;
use App\Models\Lesson;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mapel
 * 
 * @property int $id
 * @property string $name
 * 
 * @property Collection|Competence[] $competences
 * @property Collection|Lesson[] $lessons
 * @property Collection|Post[] $posts
 *
 * @package App\Models\Base
 */
class Mapel extends Model
{
	protected $table = 'mapels';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	public function competences()
	{
		return $this->hasMany(Competence::class);
	}

	public function lessons()
	{
		return $this->hasMany(Lesson::class);
	}

	public function posts()
	{
		return $this->hasMany(Post::class);
	}
}
