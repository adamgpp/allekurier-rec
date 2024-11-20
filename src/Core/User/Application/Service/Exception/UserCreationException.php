<?php

declare(strict_types=1);

namespace App\Core\User\Application\Service\Exception;

use App\Core\Common\Domain\ValueObject\Email;
use Symfony\Component\Uid\Ulid;

final class UserCreationException extends \DomainException
{
    public static function userWithIdAlreadyExists(Ulid $id): self
    {
        return new self(sprintf(
            'User with ID `%s` already exists.',
            $id->toRfc4122(),
        ));
    }

    public static function userWithEmailAlreadyExists(Email $email): self
    {
        return new self(sprintf(
            'User with email `%s` already exists.',
            $email->value,
        ));
    }
}
