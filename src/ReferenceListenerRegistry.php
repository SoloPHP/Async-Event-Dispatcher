<?php

/**
 * Reference Listener Registry
 *
 * PHP version 8
 */

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

/**
 * Registry for storing listener references by event class
 */
final class ReferenceListenerRegistry
{
    /**
     * Listener references indexed by event class and priority
     *
     * @var array<string, array<int, list<ListenerReference>>>
     */
    private array $referencesByEvent = [];

    /**
     * Add listener reference for event class
     *
     * @param string            $eventClass Event class name
     * @param ListenerReference $reference  Listener reference
     * @param int               $priority   Priority (higher first)
     *
     * @return void
     */
    public function addReference(
        string $eventClass,
        ListenerReference $reference,
        int $priority = 0
    ): void {
        $this->referencesByEvent[$eventClass][$priority][] = $reference;
    }

    /**
     * Get all listener references for event, ordered by priority
     *
     * @param object $event Event instance
     *
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
