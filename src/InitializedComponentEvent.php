<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * InitializedComponentEvent.php
 * @copyright Copyright (c) 2016 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application;

use MattFerris\Events\EventInterface;
use MattFerris\Component\ComponentInterface;

class InitializedComponentEvent implements EventInterface
{
    /**
     * @var \MattFerris\Component\ComponentInterface Component
     */
    protected $component;

    /**
     * @param \MattFerris\Component\ComponentInterface $component Component
     */
    public function __construct(ComponentInterface $component)
    {
        $this->component = $component;
    }

    /**
     * @return \MattFerris\Component\ComponentInterface Component
     */
    public function getComponent()
    {
        return $this->component;
    }
}
