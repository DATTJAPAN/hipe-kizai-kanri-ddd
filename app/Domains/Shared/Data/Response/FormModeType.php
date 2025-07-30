<?php

declare(strict_types=1);

namespace App\Domains\Shared\Data\Response;

enum FormModeType: string
{
    case EDIT = 'edit';
    case CREATE = 'create';
    case MANAGE = 'manage';
    case VIEW = 'view';
    case UNKNOWN = 'unknown';
}
