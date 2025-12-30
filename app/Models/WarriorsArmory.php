<?php

namespace App\Models;

/**
 * 
 *
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereAmmunition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereAmmunitionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereAttack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereBoots($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereDefence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereHelm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereLeftHand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereLegs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereRightHand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsArmory whereWarriorId($value)
 * @mixin \Eloquent
 */
class WarriorsArmory extends SoldierArmory {}
