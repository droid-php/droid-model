<?php

namespace Droid\Test\Model\Inventory;

use PHPUnit_Framework_TestCase;

use Droid\Model\Inventory\Host;
use Droid\Model\Inventory\HostGroup;
use Droid\Model\Inventory\Inventory;
use Droid\Model\Project\Environment;

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

    public function testAddHostWithoutEnvPrivateWillNotSetDroidIP()
    {
        $host = new Host('some-host');
        $inventory = new Inventory;

        $host->droid_ip = null;
        $host->public_ip = '192.0.2.1';
        $host->private_ip = '192.0.2.2';

        $inventory->addHost($host);
        $this->assertNull($host->droid_ip);
    }

    public function testAddHostWhenEnvSettingIsTrueAndDroidIpIsAlreadySetWillNotSetDroidIP()
    {
        $host = new Host('some-host');
        $inventory = new Inventory;
        $environment = new Environment;

        $inventory->setEnvironment($environment);

        $host->droid_ip = '192.0.2.0';
        $host->public_ip = '192.0.2.1';
        $host->private_ip = '192.0.2.2';

        $environment->droid_use_private_net = true;

        $inventory->addHost($host);
        $this->assertSame('192.0.2.0', $host->droid_ip);
    }

    public function testAddHostWhenEnvSettingIsFalseAndDroidIpIsUnsetWillNotSetDroidIP()
    {
        $host = new Host('some-host');
        $inventory = new Inventory;
        $environment = new Environment;

        $inventory->setEnvironment($environment);

        $host->droid_ip = null;
        $host->public_ip = '192.0.2.1';
        $host->private_ip = '192.0.2.2';

        $environment->droid_use_private_net = false;

        $inventory->addHost($host);
        $this->assertNull($host->droid_ip);
    }

    public function testAddHostWhenEnvSettingIsTrueAndDroidIpIsUnsetWillSetDroidIP()
    {
        $host = new Host('some-host');
        $inventory = new Inventory;
        $environment = new Environment;

        $inventory->setEnvironment($environment);

        $host->droid_ip = null;
        $host->public_ip = '192.0.2.1';
        $host->private_ip = '192.0.2.2';

        $environment->droid_use_private_net = true;

        $inventory->addHost($host);
        $this->assertSame('192.0.2.2', $host->droid_ip);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage it is missing the "private_ip" required by the environment setting "droid_use_private_net"
     */
    public function testAddHostWhenEnvSettingIsTrueAndDroidIpIsUnsetAndPrivateIpIsMissingWillThrowException()
    {
        $host = new Host('some-host');
        $inventory = new Inventory;
        $environment = new Environment;

        $inventory->setEnvironment($environment);

        $host->droid_ip = null;
        $host->public_ip = '192.0.2.1';
        $host->private_ip = null;

        $environment->droid_use_private_net = true;

        $inventory->addHost($host);
    }

    /**
     * @dataProvider singleNameExpressionProvider
     * @param string $nameExpression
     * @param array $hosts
     * @param array $groups
     * @param array $expectedNames
     */
    public function testGetHostNameWithSingleNameWillReturnNamedHostOrHostsOfNamedGroup(
        $nameExpression,
        $hosts,
        $groups,
        $expectedNames
    ) {
        $inventory = new Inventory;

        $this->populateInventory($inventory, $hosts, $groups);

        $this->assertSame(
            $expectedNames,
            array_keys($inventory->getHostsByName($nameExpression))
        );
    }

    public function singleNameExpressionProvider()
    {
        return array(
            array(
                'Empty name' => '',
                array(),
                array(),
                array(),
            ),
            array(
                'Trimmed empty name' => '   ',
                array(),
                array(),
                array(),
            ),
            array(
                'Trimmed empty name with commas' => ' ,, ,  ',
                array(),
                array(),
                array(),
            ),
            array(
                'Single host name' => 'some-host',
                array('some-host', 'some-other-host'),
                array(),
                array('some-host'),
            ),
            array(
                'Trimmed single host name' => '    some-host       ',
                array('some-host', 'some-other-host'),
                array(),
                array('some-host'),
            ),
            array(
                'Single group name' => 'some-group',
                array('some-host1', 'some-host2', 'some-other-host'),
                array(
                    'some-group' => array('some-host1', 'some-host2'),
                    'some-other-group' => array('some-other-host'),
                ),
                array('some-host1', 'some-host2'),
            ),
        );
    }

    /**
     * @dataProvider mooltiNameExpressionProvider
     * @param string $nameExpression
     * @param array $hosts
     * @param array $groups
     * @param array $expectedNames
     */
    public function testGetHostNameWithMultpleNameWillReturnNamedHostOrHostsOfNamedGroup(
        $nameExpression,
        $hosts,
        $groups,
        $expectedNames
    ) {
        $inventory = new Inventory;

        $this->populateInventory($inventory, $hosts, $groups);

        $this->assertSame(
            $expectedNames,
            array_keys($inventory->getHostsByName($nameExpression))
        );
    }

    public function mooltiNameExpressionProvider()
    {
        return array(
            'Space separated host names' => array(
                'some-host some-other-host',
                array('some-host', 'some-other-host'),
                array(),
                array('some-host', 'some-other-host'),
            ),
            'Trimmed comma separated names' => array(
                ' some-host ,, , some-other-host ',
                array('some-host', 'some-other-host'),
                array(),
                array('some-host', 'some-other-host'),
            ),
            array(
                'Space separated list of host and group names' => 'some-group some-other-host',
                array('some-host1', 'some-host2', 'some-other-host'),
                array(
                    'some-group' => array('some-host1', 'some-host2'),
                    'some-other-group' => array('some-other-host'),
                ),
                array('some-host1', 'some-host2', 'some-other-host'),
            ),
        );
    }

    private function populateInventory($inventory, $hosts, $groups)
    {
        foreach ($hosts as $name) {
            $inventory->addHost(new Host($name));
        }

        foreach ($groups as $groupname => $hosts) {
            $group = new HostGroup($groupname);
            $inventory->addHostGroup($group);
            foreach ($hosts as $name) {
                $group->addHost($inventory->getHost($name));
            }
        }
    }
}
