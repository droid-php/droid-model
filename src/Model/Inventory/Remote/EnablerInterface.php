<?php

namespace Droid\Model\Inventory\Remote;

use Droid\Model\Inventory\Remote\Check\HostCheckInterface;

/**
 * Enable remote execution of droid commands.
 */
interface EnablerInterface
{
    /**
     * Enable droid execution on the supplied remote host.
     *
     * @param AbleInterface $host
     */
    public function enable(AbleInterface $host);

    /**
     * Add to the sequence of checks performed on a remote host.
     *
     * @param \Droid\Model\Inventory\Remote\Check\HostCheckInterface $check
     */
    public function addHostCheck(HostCheckInterface $check);
}
