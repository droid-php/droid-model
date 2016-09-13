<?php

namespace Droid\Model\Inventory\Remote\Check;

use Droid\Model\Inventory\Remote\AbleInterface;

/**
 * Perform a check on a Host.
 */
interface HostCheckInterface
{
    public function configure(array $options);

    /**
     * Perform the check.
     *
     * @param \Droid\Model\Inventory\Remote\AbleInterface $host
     *
     * @throws \Droid\Model\Inventory\Remote\Check\CheckFailureException
     * @throws \Droid\Model\Inventory\Remote\Check\UnrecoverableCheckFailureException
     *
     * @return bool
     */
    public function check(AbleInterface $host);
}
