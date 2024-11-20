<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\User\Application\EventListener;

use App\Core\Common\Domain\Notification\NotificationInterface;
use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Application\EventListener\SendEmailUserCreatedEventSubscriberListener;
use App\Core\User\Domain\Event\UserCreatedEvent;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

final class SendEmailUserCreatedEventSubscriberListenerTest extends TestCase
{
    public function testSendEmailIsTriggeredOnUserCreatedEvent(): void
    {
        /** @var NotificationInterface&MockObject $mailer */
        $mailer = $this->createMock(NotificationInterface::class);
        $mailer
            ->expects(self::once())
            ->method('sendEmail')
            ->with(
                new Email('user@example.com'),
                'Utworzono użytkownika',
                'Zarejestrowano konto w systemie. Aktywacja konta trwa do 24h'
            );

        $listener = new SendEmailUserCreatedEventSubscriberListener($mailer);

        $event = new UserCreatedEvent(new User(new Ulid(), new Email('user@example.com')));

        $listener->send($event);
    }
}
