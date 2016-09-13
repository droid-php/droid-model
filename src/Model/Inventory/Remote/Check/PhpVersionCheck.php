<?php

namespace Droid\Model\Inventory\Remote\Check;

use Droid\Model\Inventory\Remote\AbleInterface;

/**
 * This check asserts that the version of PHP installed on a Host is sufficient.
 */
class PhpVersionCheck extends AbstractHostCheck
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
        $logContext = array(
            'host' => $host->getName(),
            'ver' => $this->minPhpVersion,
        );

        $this->logger->info(
            'Begin check for minimum PHP version ({ver}).',
            $logContext
        );

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

        $this->logger->info(
            'Finished check for minimum PHP version. Success.',
            $logContext
        );

        return true;
    }
}
