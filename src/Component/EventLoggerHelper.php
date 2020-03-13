<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * EventLoggerHelper.php
 * @copyright Copyright (c) 2016 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application\Component;

use MattFerris\Logging\LoggerInterface;
use ReflectionClass;
use RuntimeException;

class EventLoggerHelper implements ComponentHelperInterface
{
    /**
     * @var MattFerris\Logging\LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @param MattFerris\Logging\LoggerInterface $logger
     * @param string $namespace
     */
    public function __construct(LoggerInterface $logger, $namespace = null)
    {
        if (is_null($namespace)) {
            $namespace = (new ReflectionClass($this))->getNamespaceName();
        }

        $this->logger = $logger;
        $this->namespace = $namespace;
    }

    /**
     * @returns void
     */
    public function execute()
    {
        $class = $this->namespace."\\DomainEventLoggerHelpers";
        if (class_exists($class)) {
            $class::addHelpers($this->logger);
        } else {
            throw new RuntimeException($class.' doesn\'t exist');
        }
    }
}
