<?php

declare(strict_types=1);

namespace App\Core\Invoice\Domain\ValueObject;

use App\Core\Common\Domain\ValueObject\Exception\ValueObjectValidationException;

final class Amount
{
    public function __construct(public readonly int $value)
    {
        if (0 >= $value) {
            throw ValueObjectValidationException::withError('An amount value should be greater than 0.');
        }
    }
}
