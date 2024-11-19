<?php

namespace App\Tests\Unit\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceHandler;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceWriteRepositoryInterface;
use App\Core\Invoice\Domain\ValueObject\Amount;
use App\Core\User\Domain\Exception\UserNotFoundException;
use App\Core\User\Domain\Repository\UserWriteRepositoryInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

final class CreateInvoiceHandlerTest extends TestCase
{
    private UserWriteRepositoryInterface&MockObject $userRepository;

    private InvoiceWriteRepositoryInterface&MockObject $invoiceRepository;

    private CreateInvoiceHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new CreateInvoiceHandler(
            $this->invoiceRepository = $this->createMock(
                InvoiceWriteRepositoryInterface::class
            ),
            $this->userRepository = $this->createMock(
                UserWriteRepositoryInterface::class
            )
        );
    }

    public function test_handle_success(): void
    {
        $user = new User(new Ulid(), new Email('test@email.com'));
        $invoice = new Invoice(new Ulid(), $user, new Amount(12500));

        $this->userRepository->expects(self::once())
            ->method('getByEmail')
            ->willReturn($user);

        $this->invoiceRepository->expects(self::once())
            ->method('save')
            ->with($invoice);

        $this->invoiceRepository->expects(self::once())
            ->method('flush');

        $this->handler->__invoke(new CreateInvoiceCommand(
            $invoice->getId(),
            $invoice->getUser()->getEmail(),
            $invoice->getAmount(),
        ));
    }

    public function test_handle_user_not_exists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepository->expects(self::once())
            ->method('getByEmail')
            ->willThrowException(new UserNotFoundException());

        $this->handler->__invoke(
            new CreateInvoiceCommand(new Ulid(), new Email('test@email.com'), new Amount(1))
        );
    }
}
