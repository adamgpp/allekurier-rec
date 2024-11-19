<?php

declare(strict_types=1);

namespace App\Core\Invoice\Domain\Feature\Exception;

use App\Core\Common\Domain\ValueObject\Email;
use Symfony\Component\Uid\Ulid;

final class InvoiceCreationException extends \DomainException
{
    public static function invoiceWithIdAlreadyExists(Ulid $id): self
    {
        return new self(sprintf(
            'Invoice with ID `%s` already exists.',
            $id->toRfc4122(),
        ));
    }

    public static function userNotActive(Ulid $userId): self
    {
        return new self(sprintf(
            'User with ID `%s` is not active.',
            $userId->toRfc4122(),
        ));
    }

    public static function userWithEmailNotExists(Email $email): self
    {
        return new self(sprintf(
            'User with email `%s` not exists.',
            $email->value,
        ));
    }
}
