<?php

declare(strict_types=1);

namespace App\Core\Invoice\Domain\Feature\InvoiceCreation;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Invoice\Domain\ValueObject\Amount;
use Symfony\Component\Uid\Ulid;

interface InvoiceCreationInterface
{
    public function createInvoice(Ulid $id, Email $userEmail, Amount $amount): void;
}
