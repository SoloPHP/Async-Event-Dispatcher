<?php

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher\Adapter;

use Solo\AsyncEventDispatcher\EventMessage;

interface AsyncAdapterInterface
{
    public function enqueue(EventMessage $message): void;

    /**
     * @param callable(EventMessage):void $handler
     */
    public function consume(callable $handler): void;
}


