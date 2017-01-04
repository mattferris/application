<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * ApplicationInterface.php
 * @copyright Copyright (c) 2016 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application;

interface ApplicationInterface
{
    /**
     * Run the application
     *
     * @param callable $run Optional, the main function to start the application
     * @return mixed The return value of the $run call
     */
    public function run(callable $run = null);
}
