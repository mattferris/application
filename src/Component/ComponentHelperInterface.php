<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * ComponentHelperInterface.php
 * @copyright Copyright (c) 2020 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application\Component;

interface ComponentHelperInterface
{
    /**
     * @returns void
     */
    public function execute();
}
