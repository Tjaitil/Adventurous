<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $username
 * @property int $warrior_id
 * @property string|null $helm
 * @property string|null $ammunition
 * @property int $ammunition_amount
 * @property string|null $body
 * @property string|null $right_hand
 * @property string|null $left_hand
 * @property string|null $legs
 * @property string|null $boots
 * @property int $attack
 * @property int $defence
 * @property-read \App\Models\Soldier $soldier
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereAmmunition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereAmmunitionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereAttack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereBoots($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereDefence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereHelm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereLeftHand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereLegs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereRightHand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoldierArmory whereWarriorId($value)
 *
 * @mixin \Eloquent
 */
class SoldierArmory extends Model
{
    /**
     * @use HasFactory<\Database\Factories\SoldierArmoryFactory>
     */
    use HasFactory;

    public $primaryKey = 'id';

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'warriors_armory';

    protected $guarded = [];

    protected $appends = [
        'attack',
        'defence',
    ];

    public function getAttackAttribute(): int
    {
        return ArmoryItemsData::whereIn('item', [
            $this->helm,
            $this->ammunition,
            $this->left_hand,
            $this->body,
            $this->right_hand,
            $this->boots,
        ])->sum('attack') + 10;
    }

    public function getDefenceAttribute(): int
    {
        return ArmoryItemsData::whereIn('item', [
            $this->helm,
            $this->ammunition,
            $this->left_hand,
            $this->body,
            $this->right_hand,
            $this->boots,
        ])->sum('defence') + 15;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Soldier, $this>
     */
    public function soldier(): BelongsTo
    {
        return $this->belongsTo(Soldier::class, 'id', 'id')->withDefault();
    }
}
