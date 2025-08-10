<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Solo\AsyncEventDispatcher\Adapter\SoloTaskQueueAdapter;
use Solo\AsyncEventDispatcher\EventMessage;

final class SoloTaskQueueAdapterTest extends TestCase
{
    public function testEnqueueAndConsumeCallsHandler(): void
    {
        $calls = [];

        $fakeQueue = new class ($calls) implements \Solo\TaskQueue\TaskQueueInterface {
            private array $tasks = [];

            public function __construct(private array &$calls)
            {
            }

            public function install(): void
            {
            }

            public function addTask(
                string $name,
                array $payload,
                ?\DateTimeImmutable $scheduledAt = null,
                ?\DateTimeImmutable $expiresAt = null
            ): int {
                $this->tasks[] = [$name, $payload];
                return count($this->tasks);
            }

            public function processPendingTasks(callable $callback, int $limit = 10, ?string $onlyType = null): void
            {
                foreach ($this->tasks as [$n, $p]) {
                    if ($onlyType !== null && ($p['type'] ?? null) !== $onlyType) {
                        continue;
                    }
                    $callback($n, $p);
                }
                $this->calls[] = 'processed';
            }
        };

        $adapter = new SoloTaskQueueAdapter($fakeQueue);

        $adapter->enqueue(new EventMessage(
            eventClass: 'E',
            eventPayload: 'payload',
            listenerClass: 'L',
            listenerMethod: 'on'
        ));

        $handled = [];
        $adapter->consume(function (EventMessage $env) use (&$handled): void {
            $handled[] = [
                $env->eventClass,
                $env->listenerClass,
                $env->listenerMethod,
                $env->eventPayload,
            ];
        });

        self::assertSame([
            ['E', 'L', 'on', 'payload'],
        ], $handled);
    }
}


