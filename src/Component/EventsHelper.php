<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * EventsHelper.php
 * @copyright Copyright (c) 2020 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application\Component;

use MattFerris\Events\EventDispatcherInterface;
use ReflectionClass;
use RuntimeException;

class EventsHelper
{
    /**
     * @var MattFerris\Events\EventDispatcherInterface;
     */
    protected $dispatcher;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @param MattFerris\Events\EventDispatcherInterface
     * @param string $namespace
     */
    public function __construct(EventDispatcherInterface $dispatcher, $namespace = null)
    {
        if (is_null($namespace)) {
            $namespace = (new ReflectionClass($this))->getNamespaceName();
        }

        $this->dispatcher = $dispatcher;
        $this->namespace = $namespace;
    }

    /**
     * @returns void
     */
    public function help()
    {
        $class = $this->namespace."\\DomainEvents";
        if (class_exists($class)) {
            $class::setDispatcher($this->dispatcher);
        } else {
            throw new RuntimeException("$class doesn't exist");
        }
    }
}
