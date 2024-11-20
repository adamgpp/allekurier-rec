<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\User\Application\Service;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Application\Service\Exception\UserCreationException;
use App\Core\User\Application\Service\UserCreatorService;
use App\Core\User\Application\Service\Validation\UserCreationValidationInterface;
use App\Core\User\Domain\Event\UserCreatedEvent;
use App\Core\User\Domain\Repository\UserWriteRepositoryInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Ulid;

final class UserCreatorServiceTest extends TestCase
{
    private UserWriteRepositoryInterface&MockObject $userWriteRepository;
    private UserCreationValidationInterface&MockObject $userCreationValidator;
    private EventDispatcherInterface&MockObject $eventDispatcher;
    private UserCreatorService $userCreatorService;

    protected function setUp(): void
    {
        $this->userWriteRepository = $this->createMock(UserWriteRepositoryInterface::class);
        $this->userCreationValidator = $this->createMock(UserCreationValidationInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->userCreatorService = new UserCreatorService(
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

        $this->userCreatorService->createUser($id, $email);
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

        $this->userCreatorService->createUser($id, $email);
    }
}
