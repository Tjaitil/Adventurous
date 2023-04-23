<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ArcheryShopData extends Model
{
    protected $table = 'archery_shop_data';
    public $timestamps = false;
    protected $appends = [
        'price',
        'type',
        'required_level',
    ];

    public function getPriceAttribute()
    {
        return $this->itemData()->value('price');
    }

    public function getTypeAttribute()
    {
        return $this->itemData()->value('type');
    }

    public function getRequiredLevelAttribute()
    {
        return $this->itemData()->value('level');
    }

    public function itemData()
    {
        return $this->belongsTo(ArmoryItemsData::class, 'item_id', 'item_id');
    }
}
