<?php

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

use Solo\AsyncEventDispatcher\Adapter\AsyncAdapterInterface;

final class AsyncEventDispatcher
{
    public function __construct(
        private readonly ReferenceListenerRegistry $registry,
        private readonly AsyncAdapterInterface $adapter,
        private readonly SerializerInterface $serializer = new NativeSerializer()
    ) {
    }

    public function dispatch(object $event): void
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
    }
}


