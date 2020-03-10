<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * Application.php
 * @copyright Copyright (c) 2016 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application;

use InvalidArgumentException;
use MattFerris\Application\Component\ComponentInterface;
use MattFerris\DependencyResolver\DependencyGraph;
use MattFerris\Di\ContainerInterface;
use ReflectionClass;

class Application implements ApplicationInterface
{
    /**
     * @var array An array of components
     */
    protected $components = [];

    /**
     * @var \MattFerris\Di\ContainerInterface The service container
     */
    protected $container;

    /**
     * @var array Provider Dependencies
     */
    protected $dependencies = null;

    /**
     * @param \MattFerris\Di\ContainerInterface $container the service container
     * @param array An array of components
     */
    public function __construct(ContainerInterface $container, array $components)
    {
        $this->container = $container;
        $this->dependencies = new DependencyGraph();

        $this->container->set('App', $this);

        foreach ($components as $comp) {
            $this->components[$comp] = $container->injectConstructor($comp);
            $this->components[$comp]->init($this);
        }
    }

    /**
     * Register dependencies for a provider
     *
     * @param string $class The provider class
     * @param array $dependencies An array of dependencies
     */
    public function registerDependencies($class, array $dependencies)
    {
        $this->dependencies->addDependency($class, $dependencies);
    }

    /**
     * {@inheritDoc}
     */
    public function run(callable $run = null)
    {
        foreach ($this->dependencies->resolve() as $item) {
            $object = $this->container->injectConstructor($item->getObject());
            $object->load();
        }

        if (!is_null($run)) {
            $isArray = is_array($run);
            if ($isArray && is_object($run[0])) {
                // object->method()
                return $this->container->injectMethod($run[0], $run[1]);
            } elseif ($isArray) {
                // class::method()
                return $this->container->injectStaticMethod($run[0], $run[1]);
            } else {
                // function() or Closure
                return $this->container->injectFunction($run);
            }
        }
    }
}
