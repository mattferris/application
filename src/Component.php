<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * Component.php
 * @copyright Copyright (c) 2016 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application;

use MattFerris\Component\ComponentInterface;
use MattFerris\Di\ContainerInterface;
use MattFerris\Events\DispatcherInterface;

class Component implements ComponentInterface
{
    /**
     * @var \MattFerris\Di\ContainerInterface The service container instance
     */
    protected $container;

    /**
     * @var \MattFerris\Events\DispatcherInterface The event dispatcher instance
     */
    protected $eventDispatcher;

    /**
     * @var array List of providers to load
     */
    protected $providers = ['Services', 'Events'];

    /**
     * @var array Collection of providers instances
     */
    protected $loadedProviders = [];

    /**
     * @param \MattFerris\Di\ContainerInterface $container The service container
     * @param \MattFerris\Events\DispatcherInterface $dispatcher The event dispatcher
     */
    public function __construct(
        ContainerInterface $container,
        DispatcherInterface $eventDispatcher
        )
    {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        // get component namespace
        $parts = explode('\\', get_class($this));
        array_pop($parts);
        $namespace = implode('\\', $parts);

        // attempt to load providers from component namespace
        foreach ($this->providers as $provider) {
            $providerClass = $namespace.'\\'.$provider.'Provider';

            if (!class_exists($providerClass)) {
                continue;
            }

            // bundles must implement \MattFerris\Provider\ProviderInterface
            if (in_array('MattFerris\Provider\ProviderInterface', class_implements($providerClass))) {
                $this->loadedProviders[$provider] =
                    $this->container->injectConstructor($providerClass);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function load()
    {
        if (isset($this->loadedProviders['Services'])) {
            $this->container->register($this->loadedProviders['Services']);
        }

        if (isset($this->loadedProviders['Events'])) {
            $this->eventDispatcher->register($this->loadedProviders['Events']);
        }
    }
}
