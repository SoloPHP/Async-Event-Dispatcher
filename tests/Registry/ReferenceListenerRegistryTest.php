<?php

declare(strict_types=1);

namespace Tests\Registry;

use PHPUnit\Framework\TestCase;
use Solo\AsyncEventDispatcher\{ListenerReference, ReferenceListenerRegistry};

final class ReferenceListenerRegistryTest extends TestCase
{
    public function testOrderingByPriorityDesc(): void
    {
        $r = new ReferenceListenerRegistry();
        $eventClass = \Tests\Fixture\UserRegistered::class;
        $r->addReference($eventClass, new ListenerReference('A'), 0);
        $r->addReference($eventClass, new ListenerReference('B'), 10);
        $r->addReference($eventClass, new ListenerReference('C'), 5);

        $result = $r->getReferencesFor(new \Tests\Fixture\UserRegistered('x'));

        $classes = array_map(fn ($ref) => $ref->class, $result);
        self::assertSame(['B', 'C', 'A'], $classes);
    }

    public function testEmptyForNoEvent(): void
    {
        $r = new ReferenceListenerRegistry();
        self::assertSame([], $r->getReferencesFor(new class {
        }));
    }
}


