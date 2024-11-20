<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\User\Application\Command\CreateUser;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Application\Command\CreateUser\CreateUserCommand;
use App\Core\User\Application\Command\CreateUser\CreateUserHandler;
use App\Core\User\Domain\Feature\UserCreationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

final class CreateUserHandlerTest extends TestCase
{
    private UserCreationInterface&MockObject $userCreator;
    private CreateUserHandler $handler;

    protected function setUp(): void
    {
        $this->userCreator = $this->createMock(UserCreationInterface::class);
        $this->handler = new CreateUserHandler($this->userCreator);
    }

    public function testHandleSuccess(): void
    {
        $command = new CreateUserCommand(new Ulid(), new Email('test@email.com'));

        $this->userCreator
            ->expects(self::once())
            ->method('createUser')
            ->with($command->id, $command->email);

        $this->handler->__invoke($command);
    }
}
