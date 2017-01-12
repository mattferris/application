<?php

namespace MattFerris\Application\UnitTests\Application;

use MattFerris\Application\Application;
use MattFerris\Di\ContainerInterface;
use MattFerris\Component\ComponentInterface;
use MattFerris\Provider\ProviderInterface;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testGlobalProviderRegistration()
    {
        $container = $this->getMock(ContainerInterface::class);
        $component = $this->getMock(ProviderComponent::class);

        $container
            ->expects($this->once())
            ->method('injectConstructor')
            ->with(ProviderComponent::class, [])
            ->willReturn($component);

        $component
            ->expects($this->once())
            ->method('provides')
            ->with($this->isInstanceOf(Application::class));

        $app = new Application($container, [ProviderComponent::class]);
    }

    public function testAddProvider()
    {
        $container = $this->getMock(ContainerInterface::class);
        $app = new Application($container, []);
        $app->addProvider('Foo', ContainerInterface::class);
    }

    /**
     * @depends testAddProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMesasage Consumer "MattFerris\Di\ContainterInterface" doesn't exist for provider "Services"
     */
    public function testAddBadProvider()
    {
        $container = $this->getMock(ContainerInterface::class);
        $app = new Application($container, []);
        $app->addProvider('Foo', 'BadProvider');
    }

    public function testRunWithClosure()
    {
        $container = $this->getMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('injectFunction')
            ->with($this->isInstanceOf('Closure'), []);

        $app = new Application($container, []);
        $app->run(function () {});
    }

    public function testRunWithInstanceMethod()
    {
        $container = $this->getMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('injectMethod')
            ->with($this, 'testRunWithInstanceMethod', []);

        $app = new Application($container, []);
        $app->run([$this, 'testRunWithInstanceMethod']);
    }

    public function testRunWithStaticMethod()
    {
        $container = $this->getMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('injectStaticMethod')
            ->with(Foo::class, 'bar', []);

        $app = new Application($container, []);
        $app->run([Foo::class, 'bar']);
    }

    public function testRunWithComponent()
    {
        $component = $this->getMock(ComponentInterface::class);
        $component->expects($this->once())->method('init');
        $component->expects($this->once())->method('load');

        $container = $this->getMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('injectConstructor')
            ->with(ComponentInterface::class, [])
            ->willReturn($component);

        $app = new Application($container, [ComponentInterface::class]);
        $app->run(function () {});
    }

    public function testConstructWithMultipleComponentPasses()
    {
        $container = $this->getMock(ContainerInterface::class);
        $container
            ->expects($this->exactly(2))
            ->method('injectConstructor')
            ->withConsecutive([Component::class, []], [ProviderComponent::class, []])
            ->will($this->onConsecutiveCalls(
                new Component(), new ProviderComponent()
            ));

        $app = new Application($container, [
            [ Component::class ], // pass 1
            [ ProviderComponent::class ] // pass 2
        ]);
    }
}

class Foo
{
    static public function bar()
    {
    }
}

class Component implements ComponentInterface
{
    public function init(array $providers = []) {}
    public function load() {}
}

class ProviderComponent implements ComponentInterface, ProviderInterface
{
    public function init(array $providers = []) {}
    public function load() {}
    public function provides($consumer) {}
}
