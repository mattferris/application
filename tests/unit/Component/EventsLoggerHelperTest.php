<?php

namespace foo;

use MattFerris\Application\Component\EventLoggerHelper;
use MattFerris\Logging\LoggerInterface;
use PHPUnit_Framework_TestCase;

class EventLoggerHelperTest extends PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $logger = $this->getMock(LoggerInterface::class);
        (new EventLoggerHelper($logger, 'foo'))->execute();
        $this->assertSame($logger, DomainEventLoggerHelpers::$foo);
    }

    /**
     * @depends testExecute
     * @expectedException \RuntimeException
     * @expectedExceptionMessage bar\DomainEventLoggerHelpers doesn't exist
     */
    public function testNonExistentClass()
    {
        $logger = $this->getMock(LoggerInterface::class);
        (new EventLoggerHelper($logger, 'bar'))->execute();
    }
}

class DomainEventLoggerHelpers
{
    static public $foo;

    static public function addHelpers($foo)
    {
        self::$foo = $foo;
    }
}
