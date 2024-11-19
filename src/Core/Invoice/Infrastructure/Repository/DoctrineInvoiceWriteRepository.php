<?php

declare(strict_types=1);

namespace App\Core\Invoice\Infrastructure\Repository;

use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceWriteRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineInvoiceWriteRepository implements InvoiceWriteRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function save(Invoice $invoice): void
    {
        $this->entityManager->persist($invoice);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
