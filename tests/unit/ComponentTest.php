<?php

namespace MattFerris\Application\UnitTest\Component;

use MattFerris\Di\ServiceProvider;
use MattFerris\Di\ContainerInterface;
use MattFerris\Provider\ProviderInterface;
use MattFerris\Provider\ConsumerInterface;

class ComponentTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $container = $this->getMock('MattFerris\Di\Di');
        $eventDispatcher = $this->getMock('MattFerris\Events\Dispatcher');
        $comp = new Component($container, $eventDispatcher);

        $container->expects($this->exactly(2))->method('injectConstructor');
        $comp->init();
    }

    /**
     * @depends testInit
     */
    public function testLoad()
    {
        $container = $this->getMock('MattFerris\Di\Di');
        $eventDispatcher = $this->getMock('MattFerris\Events\Dispatcher');
        $comp = new Component($container, $eventDispatcher);

        $container
            ->expects($this->exactly(2))
            ->method('injectConstructor')
            ->withConsecutive(
                [ServicesProvider::class, []],
                [EventsProvider::class, []]
            )
            ->will($this->onConsecutiveCalls(
                new ServicesProvider(),
                new EventsProvider()
            ));

        $comp->init();

        $container
            ->expects($this->once())
            ->method('register')
            ->with($this->isInstanceOf(ServicesProvider::class));

        $eventDispatcher
            ->expects($this->once())
            ->method('register')
            ->with($this->isInstanceOf(EventsProvider::class));

        $comp->load();
    }
}

class Component extends \MattFerris\Application\Component
{
}

class ServicesProvider extends ServiceProvider
{
    public function provides($consumer){}
}

class EventsProvider implements ProviderInterface
{
    public function provides($consumer){}
}
