<?php

namespace App\Tests\Unit\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceHandler;
use App\Core\Invoice\Domain\Feature\InvoiceCreation\InvoiceCreationInterface;
use App\Core\Invoice\Domain\ValueObject\Amount;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

final class CreateInvoiceHandlerTest extends TestCase
{
    private InvoiceCreationInterface&MockObject $invoiceCreator;

    private CreateInvoiceHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new CreateInvoiceHandler(
            $this->invoiceCreator = $this->createMock(
                InvoiceCreationInterface::class
            )
        );
    }

    public function testHandleSuccess(): void
    {
        $command = new CreateInvoiceCommand(
            new Ulid(),
            new Email('test@email.com'),
            new Amount(12500),
        );

        $this->invoiceCreator
            ->expects(self::once())
            ->method('createInvoice')
            ->with($command->id, $command->userEmail, $command->amount);

        $this->handler->__invoke($command);
    }
}
