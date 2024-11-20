<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Invoice\Domain\ValueObject;

use App\Core\Common\Domain\ValueObject\Exception\ValueObjectValidationException;
use App\Core\Invoice\Domain\ValueObject\Amount;
use PHPUnit\Framework\TestCase;

final class AmountTest extends TestCase
{
    public function testValidAmount(): void
    {
        $amount = new Amount(100);
        $this->assertSame(100, $amount->value);
    }

    public function testShouldThrowExceptionForZero(): void
    {
        $this->expectException(ValueObjectValidationException::class);
        $this->expectExceptionMessage('An amount value should be greater than 0.');

        new Amount(0);
    }

    public function testShouldThrowExceptionForNegative(): void
    {
        $this->expectException(ValueObjectValidationException::class);
        $this->expectExceptionMessage('An amount value should be greater than 0.');

        new Amount(-10);
    }
}
