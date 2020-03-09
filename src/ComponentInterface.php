<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * ComponentInterface.php
 * @copyright Copyright (c) 2020 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application;

/**
 * Defines how a unit of code (i.e. library, domain, etc...) should be
 * configured in an application.
 */
interface ComponentInterface
{
    /**
     * @param \MattFerris\Application\Application $app The application instance
     */
    public function init(Application $app);
}
