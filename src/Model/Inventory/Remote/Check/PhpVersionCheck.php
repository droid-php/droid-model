<?php

namespace Droid\Model\Inventory\Remote\Check;

use Droid\Model\Inventory\Remote\AbleInterface;

/**
 * This check asserts that the version of PHP installed on a Host is sufficient.
 */
class PhpVersionCheck implements HostCheckInterface
{
    protected $minPhpVersion = 50509;

    public function configure(array $options)
    {
        if (isset($options['min_php_version'])) {
            $this->minPhpVersion = $options['min_php_version'];
        }
        return $this;
    }

    public function check(AbleInterface $host)
    {
        $ssh = $host->getSshClient();

        $ssh->exec(array('php', '-r', '"echo PHP_VERSION_ID;"'));
        if ($ssh->getExitCode()) {
            throw new UnrecoverableCheckFailureException(
                sprintf(
                    'Unable to check the version of PHP installed:- %s',
                    $ssh->getErrorOutput()
                )
            );
        }

        $version = trim($ssh->getOutput());
        if ($version < $this->minPhpVersion) {
            throw new UnrecoverableCheckFailureException(
                sprintf(
                    'The remotely installed version of PHP is too low. Got %s; Require PHP >= %d.',
                    $version,
                    $this->minPhpVersion
                )
            );
        }

        return true;
    }
}
