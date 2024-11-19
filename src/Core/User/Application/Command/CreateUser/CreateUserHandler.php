<?php

declare(strict_types=1);

namespace App\Core\User\Application\Command\CreateUser;

use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    )
    {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $this->userRepository->save(new User(
            $command->id,
            $command->email,
        ));

        $this->userRepository->flush();
    }
}
