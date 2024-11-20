<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\Service;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Invoice\Application\Service\Validation\InvoiceCreationValidationInterface;
use App\Core\Invoice\Domain\Event\InvoiceCreatedEvent;
use App\Core\Invoice\Domain\Feature\InvoiceCreationInterface;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceWriteRepositoryInterface;
use App\Core\Invoice\Domain\ValueObject\Amount;
use App\Core\User\Domain\Repository\UserWriteRepositoryInterface;
use App\Core\User\Domain\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Ulid;

final class InvoiceCreatorService implements InvoiceCreationInterface
{
    public function __construct(
        private readonly InvoiceWriteRepositoryInterface $invoiceWriteRepository,
        private readonly UserWriteRepositoryInterface $userRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly InvoiceCreationValidationInterface $invoiceCreationValidator,
    ) {
    }

    public function createInvoice(Ulid $id, Email $userEmail, Amount $amount): void
    {
        $this->invoiceCreationValidator->assertInvoiceCanBeCreated($id, $userEmail);

        /** @var User $user */
        $user = $this->userRepository->findByEmail($userEmail);

        $invoice = new Invoice($id, $user, $amount);

        $this->invoiceWriteRepository->save($invoice);
        $this->invoiceWriteRepository->flush();

        $this->eventDispatcher->dispatch(new InvoiceCreatedEvent($invoice));
    }
}
