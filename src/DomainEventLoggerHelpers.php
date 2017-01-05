<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * DomainEvents.php
 * @copyright Copyright (c) 2016 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application;

use MattFerris\Events\AbstractLoggerHelpers;

class DomainEventLoggerHelpers extends AbstractLoggerHelpers
{
    static public function onInitializedComponentEvent(InitializedComponentEvent $e)
    {
        $class = get_class($e->getComponent());
        return 'intialized component '.$class;
    }

    static public function onAddProviderEvent(AddedProviderEvent $e)
    {
        $providerName = $e->getProviderName();
        $consumer = $e->getConsumer();
        return 'added provider '.$providerName.' for consumer '.$consumer;
    }
}
