<?php

/**
 * Async Adapter Interface
 *
 * PHP version 8
 */

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher\Adapter;

use Solo\AsyncEventDispatcher\EventMessage;

/**
 * Interface for async queue adapters
 */
interface AsyncAdapterInterface
{
    /**
     * Enqueue event message
     *
     * @param EventMessage $message Event message to enqueue
     *
     * @return void
     */
    public function enqueue(EventMessage $message): void;

    /**
     * Consume messages from queue
     *
     * @param callable(EventMessage):void $handler Message handler
     *
     * @return void
     */
    public function consume(callable $handler): void;
}
