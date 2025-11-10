<?php

namespace App\Models;

use App\Models\Base\Medium as BaseMedium;

class Medium extends BaseMedium
{
	protected $fillable = [
		'model_type',
		'model_id',
		'uuid',
		'collection_name',
		'name',
		'file_name',
		'mime_type',
		'disk',
		'conversions_disk',
		'size',
		'manipulations',
		'custom_properties',
		'generated_conversions',
		'responsive_images',
		'order_column'
	];
}
