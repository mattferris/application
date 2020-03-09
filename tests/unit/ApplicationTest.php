<?php

namespace MattFerris\Application\UnitTests\Application;

use MattFerris\Application\Application;
use MattFerris\Application\ComponentInterface;
use MattFerris\Di\ContainerInterface;
use MattFerris\Provider\ProviderInterface;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
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
    public function init(Application $app) {}
}
