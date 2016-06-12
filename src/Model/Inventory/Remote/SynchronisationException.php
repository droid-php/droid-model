<?php

namespace Droid\Model\Inventory\Remote;

use Droid\Model\Inventory\HostException;

/**
 * RuntimeException thrown in the course of synchronising versions of the droid
 * executable on remote hosts.
 */
class SynchronisationException extends HostException
{
    public function __construct(
        $host = null,
        $message = null,
        $code = null,
        $previous = null
    ) {
        $message = sprintf(
            'Unable to synchronise remote droid binary: %s',
            $message ?: 'No Message'
        );
        return parent::__construct($host, $message, $code, $previous);
    }
}
