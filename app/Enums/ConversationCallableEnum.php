<?php

namespace App\Enums;

enum ConversationCallableEnum: string
{
    case Replacer = 'replacers';
    case ServerEvent = 'serverEvents';
    case ClientEvent = 'clientEvents';
    case ClientCallBack = 'clientCallBacks';
    case Conditionals = 'conditionals';
}
