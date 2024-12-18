<?php

declare(strict_types=1);

namespace App\Core\User\Application\Command\CreateUser;

use App\Common\Bus\CommandInterface;
use App\Core\Common\Domain\ValueObject\Email;
use Symfony\Component\Uid\Ulid;

/**
 * @see CreateUserHandler
 */
final class CreateUserCommand implements CommandInterface
{
    public function __construct(
        public readonly Ulid $id,
        public readonly Email $email,
    ) {
    }
}
