<?php

declare(strict_types=1);

namespace App\Core\User\Domain\Feature\UserCreation;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\Event\UserCreatedEvent;
use App\Core\User\Domain\Feature\UserCreation\Validation\UserCreationValidationInterface;
use App\Core\User\Domain\Repository\UserWriteRepositoryInterface;
use App\Core\User\Domain\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Ulid;

final class UserCreator implements UserCreationInterface
{
    public function __construct(
        private readonly UserWriteRepositoryInterface $userWriteRepository,
        private readonly UserCreationValidationInterface $userCreationValidator,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createUser(Ulid $id, Email $email): void
    {
        $this->userCreationValidator->assertUserCanBeCreated($id, $email);

        $user = new User($id, $email);

        $this->userWriteRepository->save($user);
        $this->userWriteRepository->flush();

        $this->eventDispatcher->dispatch(new UserCreatedEvent($user));
    }
}
