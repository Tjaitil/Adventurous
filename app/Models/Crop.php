<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Crop
 *
 * @property string $crop_type
 * @property int $farmer_level
 * @property int $time
 * @property int $experience
 * @property int $seed_required
 * @property string $seed_item
 * @property int $min_crop_count
 * @property int $max_crop_count
 * @property string $location
 * @method static \Illuminate\Database\Eloquent\Builder|Crop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Crop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Crop query()
 * @method static \Illuminate\Database\Eloquent\Builder|Crop whereCropType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crop whereExperience($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crop whereFarmerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crop whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crop whereMaxCropCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crop whereMinCropCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crop whereSeedItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crop whereSeedRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crop whereTime($value)
 * @mixin \Eloquent
 */
class Crop extends Model
{
    public $timestamps = false;

    protected $table = "crops_data";
}
