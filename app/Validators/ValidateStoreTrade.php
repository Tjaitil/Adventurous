<?php

namespace App\Validators;

use App\libs\Request;
use Respect\Validation\Validator;

class ValidateStoreTrade
{

    /**
     * @throws \Exception 
     */
    public static function validate(Request $request): void
    {
        $request->validate([
            'item' => Validator::stringVal()->notEmpty(),
            'amount' => Validator::intVal(),
        ]);
    }
}
