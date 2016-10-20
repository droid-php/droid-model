<?php

namespace Droid\Model\Feature\Firewall;

interface FirewallInterface
{
    public function getRulesByHostname($name);

    public function constructAddresses($address);

    public function constructAddress($address);

    /**
     * Get the firewall policy of the Host with the supplied hostname.
     *
     * @param string $name Name of a Host.
     *
     * @return array
     */
    public function getPolicyByHostname($name);
}
