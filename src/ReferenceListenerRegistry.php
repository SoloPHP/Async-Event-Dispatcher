<?php

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

final class ReferenceListenerRegistry
{
    /** @var array<string, array<int, list<ListenerReference>>> */
    private array $referencesByEvent = [];

    public function addReference(string $eventClass, ListenerReference $reference, int $priority = 0): void
    {
        $this->referencesByEvent[$eventClass][$priority][] = $reference;
    }

    /**
     * @return list<ListenerReference>
     */
    public function getReferencesFor(object $event): array
    {
        $eventClass = $event::class;
        $collected = [];

        if (!isset($this->referencesByEvent[$eventClass])) {
            return [];
        }

        foreach ($this->referencesByEvent[$eventClass] as $priority => $refs) {
            foreach ($refs as $ref) {
                $collected[$priority][] = $ref;
            }
        }

        if ($collected === []) {
            return [];
        }

        krsort($collected);
        $ordered = [];
        foreach ($collected as $refs) {
            foreach ($refs as $ref) {
                $ordered[] = $ref;
            }
        }
        return $ordered;
    }
}


