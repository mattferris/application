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
use MattFerris\Component\ComponentInterface;
use MattFerris\DependencyResolver\DependencyGraph;
use MattFerris\Di\ContainerInterface;
use MattFerris\Provider\ProviderInterface;
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
     * @var array Global providers
     */
    protected $providers = [
        'Services' => [
            'consumer' => '\MattFerris\Di\ContainerInterface',
            'scope' => 'global'
        ],
    ];

    /**
     * @param \MattFerris\Di\ContainerInterface $container the service container
     * @param array An array of components
     */
    public function __construct(ContainerInterface $container, array $components)
    {
        $this->container = $container;

        $graph = new DependencyGraph();

        foreach ($components as $comp) {
            $deps = $this->getDependencies($comp);
            if (count($deps) > 0) {
                $graph->addDependency($comp, $deps);
            }

            foreach ($comp::providers as $name => $def) {
                $classParts = explode('\\', $comp);
                array_splice($classParts, 0, -1, $name);
                $providerClass = implode('\\', $classParts);

                $graph->addDependency($providerClass, [$comp]);
                $deps = $this->getDependencies($providerClass);
                if (count($deps) > 0) {
                    $graph->addDependency($comp, $deps);
                }
            }
        }

        foreach ($graph->resolve() as $item) {
            $instance = $container->injectConstructor($item);
            if ($item instanceof ComponentInterface) {
                $this->components[$item] = $instance;
            }
        }
    }

    /**
     * @param string $class The class to get dependencies for
     */
    protected function getDependencies($class)
    {
        $deps = [];
        $classRef = new ReflectionClass($class);
        if ($classRef->getConstructor() !== null) {
            foreach ($classRef->getConstrutor()->getParams() as $p) {
                if ($p->hasType() && class_exists($p->getType())) {
                    $deps[] = $p->getType();
                }
            }
        }
        return $deps;
    }

    /**
     * Add global providers
     *
     * @param string $providerName The provider name
     * @param string $consumer The consumer of the provider
     * @return self
     * @throws \InvalidArgumentException If $consumer class doesn't exist
     * @throws DuplicateProviderException If $providerName already exists
     */
    public function addProvider($providerName, $consumer)
    {
        if (!class_exists($consumer) && !interface_exists($consumer)) {
            throw new InvalidArgumentException(
                'Consumer "'.$consumer.'" doesn\'t exist for provider "'.$providerName.'"'
            );
        }

        if (isset($this->providers[$providerName])) {
            throw new DuplicateProviderException($providerName);
        }

        $this->providers[$providerName] = ['consumer' => $consumer, 'scope' => 'global'];

        DomainEvents::dispatch(new AddedProviderEvent($providerName, $consumer));
    }

    /**
     * {@inheritDoc}
     */
    public function run(callable $run = null)
    {
        foreach ($this->components as $component) {
            $component->load();
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
