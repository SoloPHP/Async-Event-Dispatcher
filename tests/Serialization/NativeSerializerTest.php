<?php

declare(strict_types=1);

namespace Tests\Serialization;

use PHPUnit\Framework\TestCase;
use Solo\AsyncEventDispatcher\NativeSerializer;

final class NativeSerializerTest extends TestCase
{
    public function testSerializeAndUnserialize(): void
    {
        $serializer = new NativeSerializer();
        $event = new \stdClass();
        $event->name = 'foo';

        $payload = $serializer->serialize($event);
        $restored = $serializer->unserialize($payload, \stdClass::class);

        self::assertInstanceOf(\stdClass::class, $restored);
        self::assertSame('foo', $restored->name);
    }

    public function testUnserializeWrongClassThrows(): void
    {
        $this->expectException(\RuntimeException::class);
        $serializer = new NativeSerializer();
        $payload = $serializer->serialize(new \stdClass());
        $serializer->unserialize($payload, \DateTimeImmutable::class);
    }
}


