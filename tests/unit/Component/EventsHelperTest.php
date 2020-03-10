<?php

use MattFerris\Application\Component\EventsHelper;
use MattFerris\Events\EventDispatcherInterface;

class EventsHelperTest extends PHPUnit_Framework_TestCase
{
    public function testHelp()
    {
        $eventDispatcher = $this->getMock(EventDispatcherInterface::class);
        (new EventsHelper($eventDispatcher))->help();
        $this->assertSame($eventDispatcher, DomainEvents::$foo);
    }
}

class DomainEvents
{
    static public $foo;

    static public function setDispatcher($foo)
    {
        self::$foo = $foo;
    }
}
