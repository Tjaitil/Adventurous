<?php

namespace App\Enums;

enum GameLogTypes: string
{
    case INFO = 'info';
    case ERROR = 'error';
    case WARNING = 'warning';
    case SUCCESS = 'success';
}
