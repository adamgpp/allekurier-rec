<?php

declare(strict_types=1);

namespace App\Core\Common\Domain\ValueObject;

use App\Core\Common\Domain\ValueObject\Exception\ValueObjectValidationException;

final class Email
{
    public function __construct(public readonly string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw ValueObjectValidationException::withError('An email value should be a valid email address.');
        }
    }
}
