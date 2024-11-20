<?php

declare(strict_types=1);

namespace App\Core\User\Application\Command\CreateUser;

use App\Core\User\Domain\Feature\UserCreationInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateUserHandler
{
    public function __construct(
        private readonly UserCreationInterface $userCreator,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $this->userCreator->createUser($command->id, $command->email);
    }
}
