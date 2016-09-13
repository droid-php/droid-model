<?php

namespace Droid\Model\Inventory\Remote\Check;

use Droid\Model\Inventory\Remote\AbleInterface;

/**
 * If configured with the path to a directory, this check will test for its
 * availability and set Host.workingDirectory accordingly.
 */
class WorkingDirectoryCheck implements HostCheckInterface
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
        if (! $this->workingDirPath) {
            return true;
        }

        $ssh = $host->getSshClient();

        $ssh->exec(
            array(
                sprintf('(cd %s)', $this->workingDirPath),
                '&&',
                sprintf('test -w %s', $this->workingDirPath),
            )
        );
        if ($ssh->getExitCode()) {
            return false;
        }

        $host->setWorkingDirectory($this->workingDirPath);

        return true;
    }
}
