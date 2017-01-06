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

use MattFerris\Provider\ProviderInterface;

class EventsProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function provides($consumer)
    {
        DomainEvents::setDispatcher($consumer);
    }
}
