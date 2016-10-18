<?php

namespace Droid\Test\Model\Inventory;

use PHPUnit_Framework_TestCase;

use Droid\Model\Inventory\Host;
use Droid\Model\Inventory\HostGroup;
use Droid\Model\Inventory\Inventory;

class InventoryTest extends PHPUnit_Framework_TestCase
{
    public function testGetHostGroupsByHost()
    {
        $group1 = $this
            ->getMockBuilder(HostGroup::class)
            ->setConstructorArgs(array('group_1'))
            ->setMethods(array('hasHost'))
            ->getMock()
        ;
        $group2 = $this
            ->getMockBuilder(HostGroup::class)
            ->setConstructorArgs(array('group_2'))
            ->setMethods(array('hasHost'))
            ->getMock()
        ;

        $host = new Host('some-host');
        $inventory = new Inventory;
        $inventory->addHostGroup($group1);
        $inventory->addHostGroup($group2);

        $group1
            ->method('hasHost')
            ->with($this->equalTo($host))
            ->willReturn(true)
        ;
        $group2
            ->method('hasHost')
            ->with($this->equalTo($host))
            ->willReturn(true)
        ;

        $this->assertSame(
            array($group1, $group2),
            $inventory->getHostGroupsByHost($host)
        );
    }
}
