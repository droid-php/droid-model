<?php

namespace Droid\Model\Feature\Firewall;

use RuntimeException;

use Droid\Model\Inventory\Inventory;

class Firewall implements FirewallInterface
{
    protected $inventory;

    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    public function getRulesByHostname($name)
    {
        $host = $this->inventory->getHost($name);
        $rules = [];

        foreach ($this->inventory->getHostGroupsByHost($host) as $group) {
            foreach ($group->getRules() as $rule) {
                $rules[] = $rule;
            }
        }

        foreach ($host->getRules() as $rule) {
            $rules[] = $rule;
        }
        return $rules;
    }

    public function constructAddresses($address)
    {
        $address = str_replace(' ', '', $address);
        $addresses = explode(',', $address);
        $res = [];
        foreach ($addresses as $address) {
            $add = $this->constructAddress($address);
            if (!$add || (count($add)==0)) {
                throw new RuntimeException("Can't parse: " . $address);
            }
            $res = array_merge($res, $add);
        }
        return $res;
    }

    public function constructAddress($address)
    {
        switch ($address) {
            case 'all':
                return ['0.0.0.0/0'];
        }
        $part = explode('.', $address);
        if (count($part)==4) {
            // simple ip address or subnet
            return [$address];
        }

        $part = explode(':', $address);
        if ($this->inventory->hasHost($part[0])) {
            $host = $this->inventory->getHost($part[0]);
            if (isset($part[1])) {
                switch ($part[1]) {
                    case 'public':
                        return [$host->public_ip];
                    case 'private':
                        return [$host->private_ip];
                    default:
                        throw new RuntimeException("Expected public or private: " . $part[1]);
                }
            } else {
                return [$host->public_ip];
            }
        }

        if ($this->inventory->hasHostGroup($part[0])) {
            $group = $this->inventory->getHostGroup($part[0]);
            $res = [];
            foreach ($group->getHosts() as $host) {
                if (isset($part[1])) {
                    switch ($part[1]) {
                        case 'public':
                            $res[] = $host->public_ip;
                            break;
                        case 'private':
                            $res[] = $host->private_ip;
                            break;
                        default:
                            throw new RuntimeException("Expected public or private: " . $part[1]);
                    }
                } else {
                    $res[] = $host->public_ip;
                }
            }
            return $res;
        }

    }

    /**
     * Get the firewall policy for the named host.
     *
     * The policy is constructed from the policies defined by any groups of
     * which the host is a member and the hosts own policy.
     *
     * @param string $name Name of a Host.
     *
     * @return array
     */
    public function getPolicyByHostname($name)
    {
        if (! $this->inventory->hasHost($name)) {
            return array();
        }

        $policy = array();
        $host = $this->inventory->getHost($name);

        foreach ($this->inventory->getHostGroupsByHost($host) as $group) {
            $policy = array_replace_recursive($policy, $group->getFirewallPolicy());
        }

        return array_replace_recursive($policy, $host->getFirewallPolicy());
    }
}
