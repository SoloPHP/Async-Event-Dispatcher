<?php

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher\Adapter;

use Solo\AsyncEventDispatcher\EventMessage;
use SplQueue;

final class InMemoryAdapter implements AsyncAdapterInterface
{
    /** @var SplQueue<EventMessage> */
    private SplQueue $queue;

    public function __construct()
    {
        $this->queue = new SplQueue();
    }

    public function enqueue(EventMessage $message): void
    {
        $this->queue->enqueue($message);
    }

    public function consume(callable $handler): void
    {
        while (!$this->queue->isEmpty()) {
            /** @var EventMessage $message */
            $message = $this->queue->dequeue();
            $handler($message);
        }
    }
}


