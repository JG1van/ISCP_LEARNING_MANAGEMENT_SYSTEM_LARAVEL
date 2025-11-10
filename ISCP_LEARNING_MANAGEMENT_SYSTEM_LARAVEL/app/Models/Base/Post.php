<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Mapel;
use App\Models\PostComment;
use App\Models\Serial;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 * 
 * @property int $id
 * @property int $serial_id
 * @property int $user_id
 * @property int $mapel_id
 * @property string $title
 * @property string|null $description
 * @property string $slug
 * @property string|null $link
 * @property string|null $attachment
 * @property string|null $embed
 * @property string|null $category
 * @property bool $is_task
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Mapel $mapel
 * @property Serial $serial
 * @property User $user
 * @property Collection|PostComment[] $post_comments
 * @property Collection|Task[] $tasks
 *
 * @package App\Models\Base
 */
class Post extends Model
{
	protected $table = 'posts';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'serial_id' => 'int',
		'user_id' => 'int',
		'mapel_id' => 'int',
		'is_task' => 'bool'
	];

	public function mapel()
	{
		return $this->belongsTo(Mapel::class);
	}

	public function serial()
	{
		return $this->belongsTo(Serial::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function post_comments()
	{
		return $this->hasMany(PostComment::class);
	}

	public function tasks()
	{
		return $this->hasMany(Task::class);
	}
}
