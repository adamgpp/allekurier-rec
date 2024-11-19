<?php

declare(strict_types=1);

namespace App\Core\Invoice\Infrastructure\Repository;

use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceWriteRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class DoctrineInvoiceWriteRepository implements InvoiceWriteRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }

    public function save(Invoice $invoice): void
    {
        $this->entityManager->persist($invoice);

        $events = $invoice->pullEvents();
        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
