<?php

declare(strict_types=1);

namespace App\Common\Bus;

use Symfony\Component\Messenger\MessageBusInterface;

final class CommandBus implements CommandBusInterface
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    public function dispatch(CommandInterface $command): void
    {
        try {
            $this->bus->dispatch($command);
        } catch (\Throwable $e) {
            while (false === $e instanceof \DomainException) {
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
