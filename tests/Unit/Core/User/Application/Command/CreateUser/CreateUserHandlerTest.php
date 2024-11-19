<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\User\Application\Command\CreateUser;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Application\Command\CreateUser\CreateUserCommand;
use App\Core\User\Application\Command\CreateUser\CreateUserHandler;
use App\Core\User\Domain\Repository\UserWriteRepositoryInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

final class CreateUserHandlerTest extends TestCase
{
    private UserWriteRepositoryInterface&MockObject $userRepository;
    private CreateUserHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserWriteRepositoryInterface::class);
        $this->handler = new CreateUserHandler($this->userRepository);
    }

    public function testInvokeShouldProperlySavesAndFlushUser(): void
    {
        $command = new CreateUserCommand(new Ulid(), new Email('test@email.com'));

        $this->userRepository
            ->expects(self::once())
            ->method('save')
            ->with(new User($command->id, $command->email));

        $this->userRepository
            ->expects(self::once())
            ->method('flush');

        $this->handler->__invoke($command);
    }

    public function testInvokeShouldFailWhenUserWithGivenEmailAlreadyExists(): void
    {
        $this->markTestSkipped('There is no validation yet.');
        $command = new CreateUserCommand(new Ulid(), new Email('test@email.com'));

        $this->userRepository
            ->expects(self::never())
            ->method('save');

        $this->userRepository
            ->expects(self::never())
            ->method('flush');

        $this->expectException(\Exception::class);

        $this->handler->__invoke($command);
    }
}
