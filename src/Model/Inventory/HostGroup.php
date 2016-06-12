<?php

namespace Droid\Model\Inventory;

use Droid\Model\Feature\Firewall\RuleTrait;
use Droid\Model\Project\VariableTrait;

class HostGroup
{
    private $name;
    private $hosts = [];

    use RuleTrait;
    use VariableTrait;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function addHost(Host $host)
    {
        $this->hosts[$host->getName()] = $host;
        return $this;
    }

    public function getHosts()
    {
        return $this->hosts;
    }
}
