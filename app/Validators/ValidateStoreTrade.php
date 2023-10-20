<?php

namespace App\Validators;

use App\libs\Request;
use Respect\Validation\Validator;

class ValidateStoreTrade
{

    public static function validate(Request $request)
    {
        $request->validate([
            'item' => Validator::stringVal()->notEmpty(),
            'amount' => Validator::intVal(),
        ]);
    }
}
