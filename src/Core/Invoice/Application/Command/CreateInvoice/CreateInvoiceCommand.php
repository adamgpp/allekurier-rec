<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Common\Domain\ValueObject\Email;

final class CreateInvoiceCommand
{
    public function __construct(
        public readonly Email $email,
        public readonly int $amount
    ) {}
}
