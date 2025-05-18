<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class SelectedConversationOptionValue
{
    public function __construct() {}
}
