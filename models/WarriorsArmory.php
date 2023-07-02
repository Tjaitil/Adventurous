<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class WarriorsArmory extends Model
{
    public $timestamps = false;
    protected $table = "warriors_armory";
    protected $appends = [
        "attack",
        "defence"
    ];

    public function getAttackAttribute()
    {
        return ArmoryItemsData::whereIn("item", [
            $this->helm,
            $this->ammunition,
            $this->left_hand,
            $this->body,
            $this->right_hand,
            $this->boots
        ])->sum("attack");
    }

    // getDefenceAttribute
    public function getDefenceAttribute()
    {
        return ArmoryItemsData::whereIn("item", [
            $this->helm,
            $this->ammunition,
            $this->left_hand,
            $this->body,
            $this->right_hand,
            $this->boots
        ])->sum("defence");
    }

    public function warrior()
    {
        return $this->belongsTo(Warriors::class, "warrior_id", "warrior_id");
    }
}
