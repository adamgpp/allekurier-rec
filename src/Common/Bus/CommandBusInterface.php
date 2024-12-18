<?php

declare(strict_types=1);

namespace App\Common\Bus;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command): void;
}
