<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Seeder;

class CropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Crop::insert([
            ['crop_type' => 'apple', 'farmer_level' => 30, 'time' => 1000, 'experience' => 282, 'seed_required' => 1, 'seed_item' => 'apple seed', 'min_crop_count' => 2, 'max_crop_count' => 4, 'location' => 'krasnur'],
            ['crop_type' => 'beans', 'farmer_level' => 40, 'time' => 1000, 'experience' => 400, 'seed_required' => 2, 'seed_item' => 'potato seed', 'min_crop_count' => 0, 'max_crop_count' => 3, 'location' => 'krasnur'],
            ['crop_type' => 'cabbage', 'farmer_level' => 20, 'time' => 80, 'experience' => 241, 'seed_required' => 1, 'seed_item' => 'cabbage seed', 'min_crop_count' => 1, 'max_crop_count' => 4, 'location' => 'krasnur'],
            ['crop_type' => 'carrot', 'farmer_level' => 10, 'time' => 80, 'experience' => 190, 'seed_required' => 1, 'seed_item' => 'carrot seed', 'min_crop_count' => 1, 'max_crop_count' => 4, 'location' => 'towhar'],
            ['crop_type' => 'corn', 'farmer_level' => 5, 'time' => 80, 'experience' => 158, 'seed_required' => 1, 'seed_item' => 'corn seed', 'min_crop_count' => 1, 'max_crop_count' => 4, 'location' => 'towhar'],
            ['crop_type' => 'oranges', 'farmer_level' => 35, 'time' => 400, 'experience' => 300, 'seed_required' => 2, 'seed_item' => 'oranges seed', 'min_crop_count' => 3, 'max_crop_count' => 5, 'location' => 'krasnur'],
            ['crop_type' => 'potato', 'farmer_level' => 3, 'time' => 1000, 'experience' => 123, 'seed_required' => 1, 'seed_item' => 'potato seed', 'min_crop_count' => 2, 'max_crop_count' => 5, 'location' => 'towhar'],
            ['crop_type' => 'spices', 'farmer_level' => 25, 'time' => 500, 'experience' => 273, 'seed_required' => 2, 'seed_item' => 'spices seed', 'min_crop_count' => 1, 'max_crop_count' => 3, 'location' => 'krasnur'],
            ['crop_type' => 'sugar', 'farmer_level' => 15, 'time' => 500, 'experience' => 223, 'seed_required' => 1, 'seed_item' => 'sugar cane seed', 'min_crop_count' => 1, 'max_crop_count' => 5, 'location' => 'krasnur'],
            ['crop_type' => 'tomato', 'farmer_level' => 1, 'time' => 400, 'experience' => 80, 'seed_required' => 1, 'seed_item' => 'tomato seed', 'min_crop_count' => 1, 'max_crop_count' => 4, 'location' => 'towhar'],
            ['crop_type' => 'watermelon', 'farmer_level' => 40, 'time' => 1500, 'experience' => 324, 'seed_required' => 2, 'seed_item' => 'watermelon seed', 'min_crop_count' => 1, 'max_crop_count' => 3, 'location' => 'krasnur'],
            ['crop_type' => 'wheat', 'farmer_level' => 42, 'time' => 1000, 'experience' => 500, 'seed_required' => 2, 'seed_item' => 'wheat seed', 'min_crop_count' => 0, 'max_crop_count' => 3, 'location' => 'towhar'],
        ]);
    }
}
