<?php

/**
 * In-Memory Adapter
 *
 * PHP version 8
 */

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher\Adapter;

use Solo\AsyncEventDispatcher\EventMessage;
use SplQueue;

/**
 * In-memory queue adapter for testing
 */
final class InMemoryAdapter implements AsyncAdapterInterface
{
    /**
     * Message queue
     *
     * @var SplQueue<EventMessage>
     */
    private SplQueue $queue;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->queue = new SplQueue();
    }

    /**
     * Enqueue event message
     *
     * @param EventMessage $message Event message to enqueue
     *
     * @return void
     */
    public function enqueue(EventMessage $message): void
    {
        $this->queue->enqueue($message);
    }

    /**
     * Consume messages from queue
     *
     * @param callable(EventMessage):void $handler Message handler
     *
     * @return void
     */
    public function consume(callable $handler): void
    {
        while (!$this->queue->isEmpty()) {
            /** @var EventMessage $message */
            $message = $this->queue->dequeue();
            $handler($message);
        }
    }
}
