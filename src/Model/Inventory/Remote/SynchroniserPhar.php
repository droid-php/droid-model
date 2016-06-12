<?php

namespace Droid\Model\Inventory\Remote;

/**
 * Ensure that a remote host has the same version of droid as the local host.
 *
 * This implementation uses the sha1 digest of the content of the droid
 * executable to determine whether two versions differ. It uses SSH and Secure
 * Copy clients provided by the Host model.
 */
class SynchroniserPhar implements SynchroniserInterface
{
    protected $localDroidPath;
    protected $droidBinaryName;

    /**
     * @param string $localDroidPath Path to the local droid binary file
     */
    public function __construct($localDroidPath = null)
    {
        $this->localDroidPath = $localDroidPath;
        $this->droidBinaryName = basename($localDroidPath);
    }

    public function sync(AbleInterface $host)
    {
        if (! $this->localDroidPath) {
            throw new SynchronisationException(
                $host->getName(),
                'Local droid is missing.'
            );
        }

        $synchronised = $this->remoteDroidMatches(
            $host,
            $this->getDroidBinaryDigest()
        );

        if (! $synchronised) {
            $this->uploadDroid($host, 300);
        }

        $host->setDroidCommandPrefix('php ' . $this->droidBinaryName);
    }

    private function getDroidBinaryDigest()
    {
        $digest = @sha1_file($this->localDroidPath); # suppress E_WARNING on noexist
        if ($digest === false) {
            throw new SynchronisationException(null, sprintf(
                'Unable to read the droid binary file %s.',
                $this->localDroidPath
            ));
        }
        return $digest;
    }

    private function remoteDroidMatches($host, $digest)
    {
        $ssh = $host
            ->getSshClient()
            ->exec(array(sprintf(
                'echo "%s %s" > %s.sha1 && sha1sum --status -c %s.sha1',
                $digest,
                $host->getWorkingDirectory() . '/' . $this->droidBinaryName,
                $host->getWorkingDirectory() . '/' . $this->droidBinaryName,
                $host->getWorkingDirectory() . '/' . $this->droidBinaryName
            )))
        ;
        return $ssh->getExitCode() == 0;
    }

    private function uploadDroid($host, $timeout)
    {
        $scp = $host->getScpClient();
        $scp->copy(
            $this->localDroidPath,
            $scp->getRemotePath($host->getWorkingDirectory() . '/'),
            null,
            $timeout
        );
        if ($scp->getExitCode()) {
            throw new SynchronisationException($host->getName(), sprintf(
                'Unable to upload droid: "%s".',
                implode(' ', explode("\n", $scp->getErrorOutput()))
            ));
        }
    }
}
