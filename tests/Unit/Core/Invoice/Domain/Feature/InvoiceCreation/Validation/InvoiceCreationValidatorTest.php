<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Invoice\Domain\Feature\InvoiceCreation\Validation;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Invoice\Domain\Feature\InvoiceCreation\Exception\InvoiceCreationException;
use App\Core\Invoice\Domain\Feature\InvoiceCreation\Validation\InvoiceCreationValidator;
use App\Core\Invoice\Domain\Repository\InvoiceReadRepositoryInterface;
use App\Core\User\Domain\Repository\UserReadRepositoryInterface;
use App\Core\User\Domain\Status\UserStatus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

final class InvoiceCreationValidatorTest extends TestCase
{
    private InvoiceReadRepositoryInterface&MockObject $invoiceReadRepository;
    private UserReadRepositoryInterface&MockObject $userReadRepository;
    private InvoiceCreationValidator $validator;

    protected function setUp(): void
    {
        $this->invoiceReadRepository = $this->createMock(InvoiceReadRepositoryInterface::class);
        $this->userReadRepository = $this->createMock(UserReadRepositoryInterface::class);

        $this->validator = new InvoiceCreationValidator(
            $this->invoiceReadRepository,
            $this->userReadRepository,
        );
    }

    public function testShouldNotFailWhenAllRequirementsAreFulfilled(): void
    {
        $invoiceId = new Ulid();
        $userEmail = new Email('user@example.com');

        $this->invoiceReadRepository->expects(self::once())
            ->method('existsById')
            ->with($invoiceId)
            ->willReturn(false);

        $user = [
            'id' => new Ulid(),
            'email' => $userEmail->value,
            'status' => UserStatus::ACTIVE,
        ];

        $this->userReadRepository->expects(self::once())
            ->method('findByEmail')
            ->with($userEmail)
            ->willReturn($user);

        $this->validator->assertInvoiceCanBeCreated($invoiceId, $userEmail);
    }

    public function testShouldFaiulWhenInvoiceAlreadyExists(): void
    {
        $invoiceId = new Ulid();
        $userEmail = new Email('user@example.com');

        $this->invoiceReadRepository->expects(self::once())
            ->method('existsById')
            ->with($invoiceId)
            ->willReturn(true);

        $this->userReadRepository->expects(self::never())
            ->method('findByEmail');

        $this->expectException(InvoiceCreationException::class);
        $this->expectExceptionMessage("Invoice with ID `{$invoiceId->toRfc4122()}` already exists.");

        $this->validator->assertInvoiceCanBeCreated($invoiceId, $userEmail);
    }

    public function testShouldFailWhenUserWithGivenEmailNotExists(): void
    {
        $invoiceId = new Ulid();
        $userEmail = new Email('user@example.com');

        $this->invoiceReadRepository->expects(self::once())
            ->method('existsById')
            ->with($invoiceId)
            ->willReturn(false);

        $this->userReadRepository->expects(self::once())
            ->method('findByEmail')
            ->with($userEmail)
            ->willReturn([]);

        $this->expectException(InvoiceCreationException::class);
        $this->expectExceptionMessage("User with email `{$userEmail->value}` does not exist.");

        $this->validator->assertInvoiceCanBeCreated($invoiceId, $userEmail);
    }

    public function testShouldFailWhenUserIsNotActive(): void
    {
        $invoiceId = new Ulid();
        $userEmail = new Email('user@example.com');

        $this->invoiceReadRepository->expects(self::once())
            ->method('existsById')
            ->with(self::equalTo($invoiceId))
            ->willReturn(false);

        $userId = new Ulid();

        $user = [
            'id' => $userId,
            'email' => $userEmail->value,
            'status' => UserStatus::INACTIVE,
        ];

        $this->userReadRepository->expects(self::once())
            ->method('findByEmail')
            ->with($userEmail)
            ->willReturn($user);

        $this->expectException(InvoiceCreationException::class);
        $this->expectExceptionMessage("User with ID `{$userId->toRfc4122()}` is not active.");

        $this->validator->assertInvoiceCanBeCreated($invoiceId, $userEmail);
    }
}
