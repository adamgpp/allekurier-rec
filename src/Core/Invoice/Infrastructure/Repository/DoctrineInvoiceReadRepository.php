<?php

declare(strict_types=1);

namespace App\Core\Invoice\Infrastructure\Repository;

use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceReadRepositoryInterface;
use App\Core\Invoice\Domain\Status\InvoiceStatus;
use App\Core\Invoice\Domain\ValueObject\Amount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Ulid;

final class DoctrineInvoiceReadRepository implements InvoiceReadRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getInvoicesWithGreaterAmountAndStatus(Amount $amount, InvoiceStatus $invoiceStatus): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('i')
            ->from(Invoice::class, 'i')
            ->where('i.status = :invoice_status')
            ->andWhere('i.amount > :invoice_amount')
            ->setParameter('invoice_status', $invoiceStatus->value)
            ->setParameter('invoice_amount', $amount->value)
            ->getQuery()
            ->getArrayResult();
    }

    public function existsById(Ulid $id): bool
    {
        return false === empty($this->entityManager
                ->createQueryBuilder()
                ->select('i')
                ->from(Invoice::class, 'i')
                ->where('i.id = :invoice_id')
                ->setParameter('invoice_id', $id->toBinary())
                ->getQuery()
                ->getArrayResult());
    }
}
