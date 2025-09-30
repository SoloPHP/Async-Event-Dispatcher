<?php

/**
 * Async Event Dispatcher
 *
 * PHP version 8
 */

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Solo\AsyncEventDispatcher\Adapter\AsyncAdapterInterface;

/**
 * Event dispatcher that queues listeners for asynchronous execution
 */
final readonly class AsyncEventDispatcher implements EventDispatcherInterface
{
    /**
     * Constructor
     *
     * @param ReferenceListenerRegistry $registry   Listener registry
     * @param AsyncAdapterInterface     $adapter    Queue adapter
     * @param SerializerInterface       $serializer Event serializer
     */
    public function __construct(
        private ReferenceListenerRegistry $registry,
        private AsyncAdapterInterface $adapter,
        private SerializerInterface $serializer = new NativeSerializer()
    ) {
    }

    /**
     * Dispatch event asynchronously
     *
     * @param object $event Event to dispatch
     *
     * @return object
     */
    public function dispatch(object $event): object
    {
        $payload = $this->serializer->serialize($event);

        foreach ($this->registry->getReferencesFor($event) as $reference) {
            $envelope = new EventMessage(
                eventClass: $event::class,
                eventPayload: $payload,
                listenerClass: $reference->class,
                listenerMethod: $reference->method()
            );

            $this->adapter->enqueue($envelope);
        }

        return $event;
    }
}
