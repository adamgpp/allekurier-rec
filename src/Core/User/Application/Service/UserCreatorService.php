<?php

declare(strict_types=1);

namespace App\Core\User\Application\Service;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\Event\UserCreatedEvent;
use App\Core\User\Domain\Feature\Exception\UserCreationException;
use App\Core\User\Domain\Feature\UserCreationInterface;
use App\Core\User\Domain\Repository\UserReadRepositoryInterface;
use App\Core\User\Domain\Repository\UserWriteRepositoryInterface;
use App\Core\User\Domain\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Ulid;

final class UserCreatorService implements UserCreationInterface
{
    public function __construct(
        private readonly UserWriteRepositoryInterface $userWriteRepository,
        private readonly UserReadRepositoryInterface $userReadRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function createUser(Ulid $id, Email $email): void
    {
        $this->assertUserCanBeCreated($id, $email);

        $user = new User($id, $email);

        $this->userWriteRepository->save($user);
        $this->userWriteRepository->flush();

        $this->eventDispatcher->dispatch(new UserCreatedEvent($user));
    }

    private function assertUserCanBeCreated(Ulid $id, Email $email): void
    {
        if ($this->userReadRepository->existsById($id)) {
            throw UserCreationException::userWithIdAlreadyExists($id);
        }

        if ($this->userReadRepository->existsByEmail($email)) {
            throw UserCreationException::userWithEmailAlreadyExists($email);
        }
    }
}
