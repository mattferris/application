<?php

namespace foo;

use MattFerris\Application\Component\EventsHelper;
use MattFerris\Events\DispatcherInterface;
use PHPUnit_Framework_TestCase;

class EventsHelperTest extends PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $eventDispatcher = $this->getMock(DispatcherInterface::class);
        (new EventsHelper($eventDispatcher, 'foo'))->execute();
        $this->assertSame($eventDispatcher, DomainEvents::$foo);
    }

    /**
     * @depends testExecute
     * @expectedException \RuntimeException
     * @expectedExceptionMessage bar\DomainEvents doesn't exist
     */
    public function testNonExistentClass()
    {
        $eventDispatcher = $this->getMock(DispatcherInterface::class);
        (new EventsHelper($eventDispatcher, 'bar'))->execute();
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
