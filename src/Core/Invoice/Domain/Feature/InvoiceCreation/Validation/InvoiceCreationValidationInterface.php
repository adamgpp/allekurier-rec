<?php

declare(strict_types=1);

namespace App\Core\Invoice\Domain\Feature\InvoiceCreation\Validation;

use App\Core\Common\Domain\ValueObject\Email;
use Symfony\Component\Uid\Ulid;

interface InvoiceCreationValidationInterface
{
    public function assertInvoiceCanBeCreated(Ulid $invoiceId, Email $userEmail): void;
}
