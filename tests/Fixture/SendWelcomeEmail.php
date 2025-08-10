<?php

declare(strict_types=1);

namespace Tests\Fixture;

final class SendWelcomeEmail
{
    public function __invoke(UserRegistered $event): void
    {
        file_put_contents('php://output', 'sent:' . $event->username . PHP_EOL);
    }
}


