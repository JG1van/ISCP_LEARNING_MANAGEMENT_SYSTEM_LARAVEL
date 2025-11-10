<?php

namespace App\Models;

use App\Models\Base\Product as BaseProduct;

class Product extends BaseProduct
{
	protected $fillable = [
		'id',
		'lesson_id',
		'name',
		'grade',
		'grade_category',
		'semester'
	];
}
