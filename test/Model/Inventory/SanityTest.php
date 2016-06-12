<?php

namespace Droid\Test\Model\Inventory;

use Droid\Model\Inventory\Host;
use Droid\Model\Inventory\HostException;
use Droid\Model\Inventory\HostGroup;
use Droid\Model\Inventory\Inventory;

class SanityTest extends \PHPUnit_Framework_TestCase
{
    public function testICanLoadHost()
    {
        new Host('some-host-name');
    }

    public function testICanLoadHostException()
    {
        new HostException;
    }

    public function testICanLoadHostGroup()
    {
        new HostGroup('some-group-name');
    }

    public function testICanLoadInventory()
    {
        new Inventory;
    }
}
