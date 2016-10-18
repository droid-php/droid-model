<?php

namespace Droid\Test\Model\Inventory;

use PHPUnit_Framework_TestCase;

use Droid\Model\Inventory\Host;
use Droid\Model\Inventory\HostGroup;

class HostGroupTest extends PHPUnit_Framework_TestCase
{
    public function testHasHost()
    {
        $host = $this
            ->getMockBuilder(Host::class)
            ->setConstructorArgs(array('some-host'))
            ->setMethods(array('getName'))
            ->getMock()
        ;

        $group = new HostGroup('some-group');
        $group->addHost($host);

        $this->assertTrue($group->hasHost($host));
    }
}
