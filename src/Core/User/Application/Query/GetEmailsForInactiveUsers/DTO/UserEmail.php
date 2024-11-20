<?php

declare(strict_types=1);

namespace App\Core\User\Application\Query\GetEmailsForInactiveUsers\DTO;

final class UserEmail
{
    public function __construct(public readonly string $email)
    {
    }
}
