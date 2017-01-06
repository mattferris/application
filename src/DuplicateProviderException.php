<?php

/**
 * application - A PHP application loader
 * www.bueller.ca/application
 *
 * DuplicateProviderException.php
 * @copyright Copyright (c) 2016 Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/application/license
 */

namespace MattFerris\Application;

use Exception;

class DuplicateProviderException extends Exception
{
    /**
     * @var The provider name
     */
    protected $providerName;

    /**
     * @param string $providerName The provider name
     */
    public function __construct($providerName)
    {
        $this->providerName = $providerName;
        $msg = 'Duplicate provider for "'.$providerName.'"';
        parent::__construct($msg);
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }
}
