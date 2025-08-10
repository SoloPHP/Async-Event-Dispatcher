<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Solo\AsyncEventDispatcher\Adapter\InMemoryAdapter;
use Solo\AsyncEventDispatcher\{AsyncEventDispatcher, ListenerReference, ReferenceListenerRegistry, Worker};

final class AsyncFlowTest extends TestCase
{
    public function testPublishAndConsumeWithInMemoryAdapter(): void
    {
        $registry = new ReferenceListenerRegistry();
        $registry->addReference(\Tests\Fixture\UserRegistered::class, new ListenerReference(\Tests\Fixture\SendWelcomeEmail::class));

        $adapter = new InMemoryAdapter();
        $publisher = new AsyncEventDispatcher($registry, $adapter);
        $worker = new Worker($adapter);

        $publisher->dispatch(new \Tests\Fixture\UserRegistered('john'));

        ob_start();
        $worker->run();
        $output = (string) ob_get_clean();

        $this->assertStringContainsString('sent:john', $output);
    }
}


