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
