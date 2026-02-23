<?php

namespace App\Enums;

enum AlertType: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';
}
