<?php

namespace Droid\Model\Feature\Firewall;

trait PolicyTrait
{
    protected $firewallPolicy = array();

    public function setFirewallPolicy(array $firewallPolicy)
    {
        $this->firewallPolicy = $firewallPolicy;
    }

    public function getFirewallPolicy()
    {
        return $this->firewallPolicy;
    }
}
