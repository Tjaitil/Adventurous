<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class SmithyItemRequired extends Model
{
    public $timestamps = false;

    protected $table = 'smithy_items_required';


    public function smithyItem()
    {
        return $this->belongsTo(SmithyItem::class, 'item_id');
    }
}
