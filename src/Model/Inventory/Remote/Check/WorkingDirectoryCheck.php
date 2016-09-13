<?php

namespace Droid\Model\Inventory\Remote\Check;

use Droid\Model\Inventory\Remote\AbleInterface;

/**
 * If configured with the path to a directory, this check will test for its
 * availability and set Host.workingDirectory accordingly.
 */
class WorkingDirectoryCheck extends AbstractHostCheck
{
    protected $workingDirPath;

    public function configure(array $options)
    {
        if (isset($options['working_dir_path'])) {
            $this->workingDirPath = $options['working_dir_path'];
        }
        return $this;
    }

    public function check(AbleInterface $host)
    {
        $logContext = array(
            'host' => $host->getName(),
            'path' => $this->workingDirPath,
            'fallback' => $host->getWorkingDirectory(),
        );

        if (! $this->workingDirPath) {
            $this->logger->notice(
                'Will not check for droid working directory. Droid will run from {fallback}.',
                $logContext
            );
            return true;
        }

        $this->logger->info(
            'Begin check for droid working directory ({path}).',
            $logContext
        );

        $ssh = $host->getSshClient();

        $ssh->exec(
            array(
                sprintf('(cd %s)', $this->workingDirPath),
                '&&',
                sprintf('test -w %s', $this->workingDirPath),
            )
        );
        if ($ssh->getExitCode()) {
            $this->logger->warning(
                'Finished check for droid working directory. Fail. Droid will run from {fallback}.',
                $logContext
            );
            return false;
        }

        $host->setWorkingDirectory($this->workingDirPath);

        $this->logger->info(
            'Finished check for droid working directory. Success.',
            $logContext
        );

        return true;
    }
}
