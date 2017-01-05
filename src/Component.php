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
use MattFerris\Provider\ProviderInterface;

/**
 * Defines how a unit of code (i.e. library, domain, etc...) should be
 * configured in an application.
 */
class Component implements ComponentInterface, ProviderInterface
{
    /**
     * @var \MattFerris\Di\ContainerInterface The service container instance
     */
    protected $container;

    /**
     * @var array List of providers to load
     */
    protected $providers = [
        // 'Services' => [
        //     'consumer' => '\MattFerris\Di\ContainerInterface',
        //     'scope' => 'local'
        //  ],
    ];

    /**
     * @var array Collection of providers instances
     */
    protected $loadedProviders = [];

    /**
     * @param \MattFerris\Di\ContainerInterface $container The service container
     * @param array $providers Optional list of additional providers
     */
    public function __construct(ContainerInterface $container, array $providers = [])
    {
        $this->container = $container;
        $this->providers = array_merge($providers, $this->providers);
    }

    /**
     * {@inheritDoc}
     */
    public function provides($consumer)
    {
        foreach ($this->providers as $provider => $spec) {
            if ($spec['scope'] === 'global') {
                $consumer->addProvider($provider, $spec['consumer']);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function init(array $providers = [])
    {
        $this->providers = array_merge($providers, $this->providers);

        // get component namespace
        $parts = explode('\\', get_class($this));
        array_pop($parts);
        $namespace = implode('\\', $parts);

        // attempt to load providers from component namespace
        foreach (array_keys($this->providers) as $provider) {
            $providerClass = $namespace.'\\'.$provider.'Provider';

            if (!class_exists($providerClass)) {
                continue;
            }

            // providers must implement \MattFerris\Provider\ProviderInterface
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
        foreach (array_keys($this->loadedProviders) as $provider) {
            $consumer = $this->providers[$provider]['consumer'];

            if (strpos($consumer, '\\') !== 0) {
                $consumer = '\\'.$consumer;
            }

            $this->container->injectMethod(
                $this->loadedProviders[$provider],
                'provides',
                ['consumer' => $consumer]
            );
        }
    }
}
