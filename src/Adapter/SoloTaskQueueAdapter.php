<?php

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher\Adapter;

use Solo\AsyncEventDispatcher\EventMessage;
use Solo\TaskQueue\TaskQueueInterface;
use DateTimeImmutable;

final class SoloTaskQueueAdapter implements AsyncAdapterInterface
{
    public function __construct(
        private readonly TaskQueueInterface $queue,
        private readonly string $taskName = 'dispatcher_event'
    ) {
    }

    public function enqueue(EventMessage $message): void
    {
        $this->queue->addTask(
            $this->taskName,
            [
                'type' => $this->taskName,
                'eventClass' => $message->eventClass,
                'eventPayload' => $message->eventPayload,
                'listenerClass' => $message->listenerClass,
                'listenerMethod' => $message->listenerMethod,
            ],
            new DateTimeImmutable()
        );
    }

    public function consume(callable $handler): void
    {
        $this->queue->processPendingTasks(function (string $name, array $payload) use ($handler): void {
            if ($name !== $this->taskName) {
                return;
            }

            $handler(new EventMessage(
                eventClass: (string)($payload['eventClass'] ?? ''),
                eventPayload: (string)($payload['eventPayload'] ?? ''),
                listenerClass: (string)($payload['listenerClass'] ?? ''),
                listenerMethod: (string)($payload['listenerMethod'] ?? '')
            ));
        }, 50, $this->taskName);
    }
}


