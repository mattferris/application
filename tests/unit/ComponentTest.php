<?php

namespace MattFerris\Application\UnitTest\Component;

use MattFerris\Di\ServiceProvider;
use MattFerris\Provider\ProviderInterface;
use MattFerris\Provider\ConsumerInterface;

class ComponentTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $container = $this->getMock('MattFerris\Di\Di');
        $container
            ->expects($this->once())
            ->method('injectConstructor')
            ->with(ServicesProvider::class, []);

        $comp = new Component($container);
        $comp->init();
    }

    public function testInitWithAdditionalProviders()
    {
        $container = $this->getMock('MattFerris\Di\ContainerInterface');
        $container
            ->expects($this->exactly(2))
            ->method('injectConstructor')
            ->withConsecutive(
                [ServicesProvider::class, []],
                [EventsProvider::class, []]
            );

        $comp = new Component($container, [
            [EventsConsumer::class, 'Events']
        ]);
        $comp->init();
    }

    /**
     * @depends testInit
     */
    public function testLoad()
    {
        $container = $this->getMock('MattFerris\Di\Di');
        $comp = new Component($container);

        $container
            ->expects($this->once())
            ->method('injectConstructor')
            ->with(ServicesProvider::class, [])
            ->willReturn(new ServicesProvider());

        $comp->init();

        $container
            ->expects($this->once())
            ->method('injectMethod')
            ->with(
                $this->isInstanceOf(ServicesProvider::class),
                'provides',
                ['consumer' => '\MattFerris\Di\ContainerInterface']
            );

        $comp->load();
    }
}

class Component extends \MattFerris\Application\Component
{
}

class ServicesProvider extends ServiceProvider
{
    public function provides($consumer)
    {
    }
}

class EventsConsumer implements ConsumerInterface
{
    public function register(ProviderInterface $provider)
    {
        $this->provides($provider);
    }
}

class EventsProvider implements ProviderInterface
{
    public function provides($consumer)
    {
    }
}
