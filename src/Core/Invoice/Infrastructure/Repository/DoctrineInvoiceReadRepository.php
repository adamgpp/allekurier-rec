<?php

declare(strict_types=1);

namespace App\Core\Invoice\Infrastructure\Repository;

use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceReadRepositoryInterface;
use App\Core\Invoice\Domain\Status\InvoiceStatus;
use App\Core\Invoice\Domain\ValueObject\Amount;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineInvoiceReadRepository implements InvoiceReadRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    /**
     * @inheritDoc
     */
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
}
