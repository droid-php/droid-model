<?php

namespace Droid\Test\Model\Feature\Firewall;

use Droid\Model\Feature\Firewall\Firewall;
use Droid\Model\Feature\Firewall\Rule;
use Droid\Model\Inventory\Inventory;

class SanityTest extends \PHPUnit_Framework_TestCase
{
    public function testICanLoadFirewall()
    {
        new Firewall($this->getMock(Inventory::class));
    }

    public function testICanLoadRule()
    {
        new Rule;
    }
}
