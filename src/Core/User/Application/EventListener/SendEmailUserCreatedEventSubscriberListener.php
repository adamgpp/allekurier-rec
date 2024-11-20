<?php

declare(strict_types=1);

namespace App\Core\User\Application\EventListener;

use App\Core\Common\Domain\Notification\NotificationInterface;
use App\Core\User\Domain\Event\UserCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SendEmailUserCreatedEventSubscriberListener implements EventSubscriberInterface
{
    public function __construct(private readonly NotificationInterface $mailer)
    {
    }

    // It should be done via another command handler.
    public function send(UserCreatedEvent $event): void
    {
        $this->mailer->sendEmail(
            $event->user->getEmail(),
            'Utworzono użytkownika',
            'Zarejestrowano konto w systemie. Aktywacja konta trwa do 24h'
        );
    }


    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::class => 'send'
        ];
    }
}
