<?php

namespace Droid\Model\Inventory;

use RuntimeException;

use Droid\Model\Project\EnvironmentAwareTrait;

class Inventory
{
    use EnvironmentAwareTrait;

    private $hosts = [];
    private $hostGroups = [];

    public function addHost(Host $host)
    {
        $this->hosts[$host->getName()] = $host;

        $this->setDroidIpToPrivateIp($host);
    }

    public function getHost($name)
    {
        if (!$this->hasHost($name)) {
            throw new RuntimeException("No such hostname: " . $name);
        }
        return $this->hosts[$name];
    }

    public function hasHost($name)
    {
        return isset($this->hosts[$name]);
    }

    public function getHosts()
    {
        return $this->hosts;
    }

    public function addHostGroup(HostGroup $hostGroup)
    {
        $this->hostGroups[$hostGroup->getName()] = $hostGroup;
    }

    public function getHostGroup($name)
    {
        if (!$this->hasHostGroup($name)) {
            throw new RuntimeException("No such host group: " . $name);
        }
        return $this->hostGroups[$name];
    }

    public function hasHostGroup($name)
    {
        return isset($this->hostGroups[$name]);
    }

    public function getHostGroups()
    {
        return $this->hostGroups;
    }

    public function getHostsByName($nameExpression)
    {
        $hosts = [];
        $names = array_filter(
            array_map(
                function ($x) {
                    return trim($x);
                },
                explode(',', str_replace(' ', ',', $nameExpression))
            ),
            'strlen'
        );
        foreach ($names as $name) {
            if ($this->hasHostGroup($name)) {
                foreach ($this->getHostGroup($name)->getHosts() as $host) {
                    $hosts[$host->getName()] = $host;
                }
            } elseif ($this->hasHost($name)) {
                $host = $this->getHost($name);
                $hosts[$host->getName()] = $host;
            } else {
                throw new RuntimeException("Unknown host (group): " . $name);
            }
        }
        return $hosts;
    }

    /**
     * Get the groups of which the supplied host is a member.
     *
     * @param Host $host
     *
     * @return HostGroup[] List of zero or more HostGroup
     */
    public function getHostGroupsByHost(Host $host)
    {
        return array_values(
            array_filter(
                $this->hostGroups,
                function ($g) use ($host) {
                    return $g->hasHost($host);
                }
            )
        );
    }

    /*
     * Environment.droid_use_private_net is used to instruct Droid to connect to
     * Inventory hosts by their private_ip, so long as the host does not already
     * have a droid_ip set.
     */
    private function setDroidIpToPrivateIp($host)
    {
        if ($host->droid_ip
            || ! $this->getEnvironment()
            || $this->getEnvironment()->droid_use_private_net !== true
        ) {
            return;
        }

        if (! $host->private_ip) {
            throw new RuntimeException(
                sprintf(
                    'I am unable to add "%s" to the Inventory because it is missing the "private_ip" required by the environment setting "droid_use_private_net".',
                    $host->getName()
                )
            );
        }

        $host->droid_ip = $host->private_ip;
    }
}
