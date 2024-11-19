<?php

declare(strict_types=1);

namespace App\Core\User\Domain;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
