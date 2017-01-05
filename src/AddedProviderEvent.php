<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * AddedProviderEvent.php
 * @copyright Copyright (c) 2016 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application;

use MattFerris\Events\EventInterface;
use MattFerris\Component\ComponentInterface;

class AddedProviderEvent extends EventInterface
{
    /**
     * @var string Provider
     */
    protected $providerName;

    /**
     * @var string Consumer
     */
    protected $consumer;

    /**
     * @param string $providerName Provider name
     * @param string $consumer Consumer
     */
    public function __construct($providerName, $consumer)
    {
        $this->providerName = $providerName;
        $this->consumer = $consumer;
    }

    /**
     * @return string Provider name
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @return string Consumer
     */
    public function getConsumer()
    {
        return $this->consumer;
    }
}
