<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Common\Domain\ValueObject;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Common\Domain\ValueObject\Exception\ValueObjectValidationException;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function validEmailProvider(): array
    {
        return [
            ['test@example.com'],
            ['valid.email@domain.co'],
            ['user.name@subdomain.domain.com'],
            ['email@domain.org'],
        ];
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testValidEmail(string $email): void
    {
        $emailObject = new Email($email);
        self::assertSame($email, $emailObject->value);
    }

    public function invalidEmailProvider(): array
    {
        return [
            ['invalid-email'],
            ['@example.com'],
            ['user@domain'],
            ['user@.com'],
            ['plainaddress'],
        ];
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testInvalidEmailThrowsException(string $invalidEmail): void
    {
        $this->expectException(ValueObjectValidationException::class);
        $this->expectExceptionMessage('An email value should be a valid email address.');

        new Email($invalidEmail);
    }
}
