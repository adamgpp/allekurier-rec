<?php

declare(strict_types=1);

namespace App\Core\User\Application\Command\CreateUser;

use App\Core\Common\Domain\ValueObject\Email;

final class CreateUserCommand
{
    public function __construct(public readonly Email $email)
    {
    }
}
