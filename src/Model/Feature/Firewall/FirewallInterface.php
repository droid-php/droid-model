<?php

namespace Droid\Model\Feature\Firewall;

interface FirewallInterface
{
    public function getRulesByHostname($name);

    public function constructAddresses($address);

    public function constructAddress($address);
}