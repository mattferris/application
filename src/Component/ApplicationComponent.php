<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * ApplicationComponent.php
 * @copyright Copyright (c) 2020 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application\Component;

class ApplicationComponent implements ComponentInterface
{
    /**
     * @var MattFerris\Events\LoggerInterface
     */
    protected $logger;

    /**
     * @var MattFerris\Events\DispatcherInterface
     */
    protected $eventDispatcher;


    /**
     * @param MattFerris\Events\LoggerInterface $logger
     * @param MattFerris\Events\DispatcherInterface $eventDispatcher
     */
    public function __construct(LoggerInterface $logger, DispatcherInterface $eventDispatcher)
    {
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * @returns void
     */
    public function load()
    {
        (new EventsHelper($eventDispatcher, 'MattFerris\\Application'))->execute();
        (new EventLoggerHelper($logger, 'MattFerris\\Application'))->execute();
    }
}

