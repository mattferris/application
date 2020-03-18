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

use MattFerris\Events\DispatcherInterface;
use ReflectionClass;
use RuntimeException;

class EventsHelper implements ComponentHelperInterface
{
    /**
     * @var MattFerris\Events\DispatcherInterface;
     */
    protected $dispatcher;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @param MattFerris\Events\DispatcherInterface
     * @param string $namespace
     */
    public function __construct(DispatcherInterface $dispatcher, $namespace = null)
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
    public function execute()
    {
        $class = $this->namespace."\\DomainEvents";
        if (class_exists($class)) {
            $class::setDispatcher($this->dispatcher);
        } else {
            throw new RuntimeException("$class doesn't exist");
        }
    }
}
