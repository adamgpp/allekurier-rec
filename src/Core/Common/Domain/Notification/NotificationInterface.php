<?php

namespace App\Core\Common\Domain\Notification;

use App\Core\Common\Domain\ValueObject\Email;

interface NotificationInterface
{
    public function sendEmail(Email $recipient, string $subject, string $message): void;
}
