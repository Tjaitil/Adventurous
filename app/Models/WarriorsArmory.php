<?php

namespace App\Models;

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
 * @property-read \App\Models\Warriors|null $warrior
 *
 * @method static \Database\Factories\SoldierArmoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory query()
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereAmmunition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereAmmunitionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereAttack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereBoots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereDefence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereHelm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereLeftHand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereLegs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereRightHand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarriorsArmory whereWarriorId($value)
 *
 * @mixin \Eloquent
 */
class WarriorsArmory extends SoldierArmory {}
