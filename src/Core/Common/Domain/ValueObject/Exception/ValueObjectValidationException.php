<?php

declare(strict_types=1);

namespace App\Core\Common\Domain\ValueObject\Exception;

final class ValueObjectValidationException extends \DomainException
{
    public static function withError(string $error): self
    {
        return new self($error);
    }
}
