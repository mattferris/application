<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * HttpComponent.php
 * @copyright Copyright (c) 2016 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application;

use MattFerris\Di\ContainerInterface;
use MattFerris\Events\DispatcherInterface as EventDispatcherInterface;
use MattFerris\Http\Routing\DispatcherInterface as HttpDispatcherInterface;

class HttpComponent extends Component
{
    /**
     * @var \MattFerris\Http\Routing\DispatcherInterface The http dispatcher instance
     */
    protected $httpDispatcher;

    /**
     * @var array List of bundles to load
     */
    protected $bundles = ['Services', 'Events', 'Routing'];

    /**
     * @param \MattFerris\Di\ContainerInterface $container The service container
     * @param \MattFerris\Events\DispatcherInterface $eventDispatcher The event dispatcher
     * @param \MattFerris\Http\Routing\DispatcherInterface $httpDispatcher The http dispatcher
     */
    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher,
        HttpDispatcherInterface $httpDispatcher
        )
    {
        parent::__construct($container, $eventDispatcher);

        $this->httpDispatcher = $httpDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function load()
    {
        parent::load();

        if (isset($this->loadedBundles['Routing'])) {
            $this->httpDispatcher->register($this->loadedBundles['Routing']);
        }
    }
}
