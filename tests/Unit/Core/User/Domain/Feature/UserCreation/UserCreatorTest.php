<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\User\Domain\Feature\UserCreation;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\Event\UserCreatedEvent;
use App\Core\User\Domain\Feature\UserCreation\Exception\UserCreationException;
use App\Core\User\Domain\Feature\UserCreation\UserCreator;
use App\Core\User\Domain\Feature\UserCreation\Validation\UserCreationValidationInterface;
use App\Core\User\Domain\Repository\UserWriteRepositoryInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Ulid;

final class UserCreatorTest extends TestCase
{
    private UserWriteRepositoryInterface&MockObject $userWriteRepository;
    private UserCreationValidationInterface&MockObject $userCreationValidator;
    private EventDispatcherInterface&MockObject $eventDispatcher;
    private UserCreator $userCreator;

    protected function setUp(): void
    {
        $this->userWriteRepository = $this->createMock(UserWriteRepositoryInterface::class);
        $this->userCreationValidator = $this->createMock(UserCreationValidationInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->userCreator = new UserCreator(
            $this->userWriteRepository,
            $this->userCreationValidator,
            $this->eventDispatcher
        );
    }

    public function testCreateUserSuccessfully(): void
    {
        $id = new Ulid();
        $email = new Email('test@example.com');

        $this->userCreationValidator->expects(self::once())
            ->method('assertUserCanBeCreated')
            ->with($id, $email);

        $user = new User($id, $email);

        $this->userWriteRepository->expects(self::once())
            ->method('save')
            ->with($user);

        $this->userWriteRepository->expects(self::once())
            ->method('flush');

        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with(new UserCreatedEvent($user));

        $this->userCreator->createUser($id, $email);
    }

    public function testCreateUserShouldFailWhenValidatorThrowsException(): void
    {
        $id = new Ulid();
        $email = new Email('test@example.com');

        $this->userCreationValidator->expects(self::once())
            ->method('assertUserCanBeCreated')
            ->with($id, $email)
            ->willThrowException(UserCreationException::userWithIdAlreadyExists($id));

        $this->expectException(UserCreationException::class);
        $this->expectExceptionMessage("User with ID `{$id->toRfc4122()}` already exists.");

        $this->userCreator->createUser($id, $email);
    }
}
