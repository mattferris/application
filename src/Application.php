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
use MattFerris\Di\ContainerInterface;
use MattFerris\Provider\ProviderInterface;

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

        foreach ($components as $component) {
            $instance = $this->container->injectConstructor($component);
            $this->components[$component] = $instance;

            if ($instance instanceof ProviderInterface) {
                $instance->provides($this);
            }
        }

        foreach ($this->components as $instance) {
            $instance->init($this->providers);
        }
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
        if (!class_exists($consumer)) {
            throw new InvalidArgumentException(
                'Consumer "'.$consumer.'" doesn\'t exist for provider "'.$provider.'"'
            );
        }

        if (isset($this->providers[$providerName])) {
            throw DuplicateProviderException($providerName);
        }

        $this->providers[$providerName] = ['consumer' => $consumer, 'scope' => 'global'];
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
