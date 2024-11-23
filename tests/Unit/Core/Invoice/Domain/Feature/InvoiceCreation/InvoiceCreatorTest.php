<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Invoice\Domain\Feature\InvoiceCreation;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Invoice\Domain\Event\InvoiceCreatedEvent;
use App\Core\Invoice\Domain\Feature\InvoiceCreation\Exception\InvoiceCreationException;
use App\Core\Invoice\Domain\Feature\InvoiceCreation\InvoiceCreator;
use App\Core\Invoice\Domain\Feature\InvoiceCreation\Validation\InvoiceCreationValidationInterface;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceWriteRepositoryInterface;
use App\Core\Invoice\Domain\ValueObject\Amount;
use App\Core\User\Domain\Repository\UserWriteRepositoryInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Ulid;

final class InvoiceCreatorTest extends TestCase
{
    private InvoiceWriteRepositoryInterface&MockObject $invoiceWriteRepository;
    private UserWriteRepositoryInterface&MockObject $userRepository;
    private EventDispatcherInterface&MockObject $eventDispatcher;
    private InvoiceCreationValidationInterface&MockObject $invoiceCreationValidator;
    private InvoiceCreator $invoiceCreator;

    protected function setUp(): void
    {
        $this->invoiceWriteRepository = $this->createMock(InvoiceWriteRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserWriteRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->invoiceCreationValidator = $this->createMock(InvoiceCreationValidationInterface::class);

        $this->invoiceCreator = new InvoiceCreator(
            $this->invoiceWriteRepository,
            $this->userRepository,
            $this->eventDispatcher,
            $this->invoiceCreationValidator
        );
    }

    public function testCreateInvoiceSuccessfully(): void
    {
        $id = new Ulid();
        $userEmail = new Email('user@example.com');
        $amount = new Amount(100);

        $this->invoiceCreationValidator->expects(self::once())
            ->method('assertInvoiceCanBeCreated')
            ->with($id, $userEmail);

        $user = new User($id, $userEmail);
        $invoice = new Invoice($id, $user, $amount);

        $this->userRepository->expects(self::once())
            ->method('findByEmail')
            ->with($userEmail)
            ->willReturn($user);

        $this->invoiceWriteRepository->expects(self::once())
            ->method('save')
            ->with($invoice);

        $this->invoiceWriteRepository->expects(self::once())
            ->method('flush');

        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with(new InvoiceCreatedEvent($invoice));

        $this->invoiceCreator->createInvoice($id, $userEmail, $amount);
    }

    public function testShouldFailWhenValidatorThrowsException(): void
    {
        $id = new Ulid();
        $userEmail = new Email('invalid@example.com');
        $amount = new Amount(100);

        $this->invoiceCreationValidator->expects(self::once())
            ->method('assertInvoiceCanBeCreated')
            ->with($id, $userEmail)
            ->willThrowException(InvoiceCreationException::invoiceWithIdAlreadyExists($id));

        $this->expectException(InvoiceCreationException::class);
        $this->expectExceptionMessage(InvoiceCreationException::invoiceWithIdAlreadyExists($id)->getMessage());

        $this->invoiceCreator->createInvoice($id, $userEmail, $amount);
    }
}
