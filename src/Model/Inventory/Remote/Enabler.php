<?php

namespace Droid\Model\Inventory\Remote;

use Droid\Model\Inventory\Remote\Check\CheckFailureException;
use Droid\Model\Inventory\Remote\Check\HostCheckInterface;
use Droid\Model\Inventory\Remote\Check\UnrecoverableCheckFailureException;

/**
 * Enable remote execution of droid commands.
 *
 * This implementation performs various HostCheckInterface checks before calling
 * on SynchroniserInterface to arrange for droid to be made available on the
 * remote host.
 */
class Enabler implements EnablerInterface
{
    protected $hostChecks = array();
    protected $synchroniser;

    public function __construct(SynchroniserInterface $synchroniser)
    {
        $this->synchroniser = $synchroniser;
    }

    public function addHostCheck(HostCheckInterface $check)
    {
        $this->hostChecks[] = $check;
        return $this;
    }

    public function enable(AbleInterface $host)
    {
        $host->unable();

        foreach ($this->hostChecks as $hostCheck) {
            try {
                $hostCheck->check($host);
            } catch (UnrecoverableCheckFailureException $e) {
                throw new EnablementException(
                    $host->getName(),
                    'Failure during host check.',
                    null,
                    $e
                );
            } catch (CheckFailureException $e) {
                # No Op
            }
        }

        try {
            $this->synchroniser->sync($host);
        } catch (SynchronisationException $e) {
            throw new EnablementException(
                $host->getName(),
                'Failure during binary synchronisation.',
                null,
                $e
            );
        }

        $host->able();
    }
}
