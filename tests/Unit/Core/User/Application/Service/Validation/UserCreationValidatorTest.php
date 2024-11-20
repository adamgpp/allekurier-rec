<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\User\Application\Service\Validation;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Application\Service\Exception\UserCreationException;
use App\Core\User\Application\Service\Validation\UserCreationValidator;
use App\Core\User\Domain\Repository\UserReadRepositoryInterface;
use App\Core\User\Domain\Status\UserStatus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

final class UserCreationValidatorTest extends TestCase
{
    private UserReadRepositoryInterface&MockObject $userReadRepository;
    private UserCreationValidator $validator;

    protected function setUp(): void
    {
        $this->userReadRepository = $this->createMock(UserReadRepositoryInterface::class);
        $this->validator = new UserCreationValidator(
            $this->userReadRepository
        );
    }

    public function testShouldNotFailWhenAllRequirementsAreFulfilled(): void
    {
        $userId = new Ulid();
        $userEmail = new Email('user@example.com');

        $this->userReadRepository->expects(self::once())
            ->method('existsById')
            ->with($userId)
            ->willReturn(false);

        $this->userReadRepository->expects(self::once())
            ->method('findByEmail')
            ->with($userEmail)
            ->willReturn([]);

        $this->validator->assertUserCanBeCreated($userId, $userEmail);
    }

    public function testShouldFailWhenUserWithGivenIdAlreadyExists(): void
    {
        $userId = new Ulid();
        $userEmail = new Email('user@example.com');

        $this->userReadRepository->expects(self::once())
            ->method('existsById')
            ->with($userId)
            ->willReturn(true);

        $this->userReadRepository->expects(self::never())
            ->method('findByEmail');

        $this->expectException(UserCreationException::class);
        $this->expectExceptionMessage("User with ID `{$userId->toRfc4122()}` already exists.");

        $this->validator->assertUserCanBeCreated($userId, $userEmail);
    }

    public function testShouldFailWhenUserWithGivenEmailAlreadyExists(): void
    {
        $userId = new Ulid();
        $userEmail = new Email('user@example.com');

        $this->userReadRepository->expects(self::once())
            ->method('existsById')
            ->with($userId)
            ->willReturn(false);

        $this->userReadRepository->expects(self::once())
            ->method('findByEmail')
            ->with($userEmail)
            ->willReturn(['id' => new Ulid(), 'email' => $userEmail, 'status' => UserStatus::ACTIVE]);

        $this->expectException(UserCreationException::class);
        $this->expectExceptionMessage("User with email `{$userEmail->value}` already exists.");

        $this->validator->assertUserCanBeCreated($userId, $userEmail);
    }
}
