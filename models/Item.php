<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

/**
 * @param int item_id
 * @param string name
 * @param int store_value
 * @param bool in_game
 * @param int towhar_rate
 * @param int golbak_rate
 * @param int snerpiir_rate
 * @param int cruendo_rate
 * @param int pvitul_rate
 * @param int khanz_rate
 * @param int ter_rate
 * @param int krasnur_rate
 * @param int hirtam_rate
 * @param int fansal_plains_rate
 * @param int tasnobil_rate
 * @param int trader_assignment_type
 * @param bool adventure_requirement
 * @param string adventure_requirement_difficulty
 * @param string adventure_requirement_role
 * @mixin \Eloquent
 */
class Item extends Model
{
    public $timestamps = false;
}
