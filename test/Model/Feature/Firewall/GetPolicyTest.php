<?php

namespace Droid\Test\Model\Feature\Firewall;

use Droid\Model\Feature\Firewall\Firewall;
use Droid\Model\Inventory\HostGroup;
use Droid\Model\Inventory\Host;
use Droid\Model\Inventory\Inventory;

class GetPolicyTest extends \PHPUnit_Framework_TestCase
{
    protected $firewall;
    protected $group;
    protected $host;
    protected $inventory;

    protected function setUp()
    {
        $this->group = $this
            ->getMockBuilder(HostGroup::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->host = $this
            ->getMockBuilder(Host::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->inventory = $this->getMock(Inventory::class);
        $this->firewall =  new Firewall($this->inventory);
    }

    public function testGetPolicyWithUnknownHostnameArgReturnsEmptyArray()
    {
        $this
            ->inventory
            ->method('hasHost')
            ->willReturn(false)
        ;

        $this->assertEquals(
            array(),
            $this->firewall->getPolicyByHostname('unknown')
        );
    }

    public function testGetPolicyWithoutHostOrGroupPolicyReturnsEmptyArray()
    {
        $this
            ->inventory
            ->method('hasHost')
            ->willReturn(true)
        ;
        $this
            ->inventory
            ->method('getHost')
            ->willReturn($this->host)
        ;
        $this
            ->inventory
            ->method('getHostGroupsByHost')
            ->willReturn(array($this->group))
        ;
        $this
            ->group
            ->method('getFirewallPolicy')
            ->willReturn(array())
        ;
        $this
            ->host
            ->method('getFirewallPolicy')
            ->willReturn(array())
        ;

        $this->assertEquals(
            array(),
            $this->firewall->getPolicyByHostname('some-host')
        );
    }

    public function testGetPolicyWithOnlyHostPolicyReturnsHostPolicy()
    {
        $this
            ->inventory
            ->method('hasHost')
            ->willReturn(true)
        ;
        $this
            ->inventory
            ->method('getHost')
            ->willReturn($this->host)
        ;
        $this
            ->inventory
            ->method('getHostGroupsByHost')
            ->willReturn(array($this->group))
        ;
        $this
            ->group
            ->method('getFirewallPolicy')
            ->willReturn(array())
        ;
        $this
            ->host
            ->method('getFirewallPolicy')
            ->willReturn(array('incoming' => 'reject'))
        ;

        $this->assertSame(
            array('incoming' => 'reject'),
            $this->firewall->getPolicyByHostname('some-host')
        );
    }

    public function testGetPolicyWithOnlyGroupPolicyReturnsGroupPolicy()
    {
        $this
            ->inventory
            ->method('hasHost')
            ->willReturn(true)
        ;
        $this
            ->inventory
            ->method('getHost')
            ->willReturn($this->host)
        ;
        $this
            ->inventory
            ->method('getHostGroupsByHost')
            ->willReturn(array($this->group))
        ;
        $this
            ->group
            ->method('getFirewallPolicy')
            ->willReturn(array('incoming' => 'allow'))
        ;
        $this
            ->host
            ->method('getFirewallPolicy')
            ->willReturn(array())
        ;

        $this->assertSame(
            array('incoming' => 'allow'),
            $this->firewall->getPolicyByHostname('some-host')
        );
    }

    public function testGetPolicyWithHostAndGroupPoliciesReturnsCombinedPolicy()
    {
        $this
            ->inventory
            ->method('hasHost')
            ->willReturn(true)
        ;
        $this
            ->inventory
            ->method('getHost')
            ->willReturn($this->host)
        ;
        $this
            ->inventory
            ->method('getHostGroupsByHost')
            ->willReturn(array($this->group))
        ;
        $this
            ->group
            ->method('getFirewallPolicy')
            ->willReturn(
                array(
                    'incoming' => 'allow',
                    'outgoing' => 'allow',
                )
            )
        ;
        $this
            ->host
            ->method('getFirewallPolicy')
            ->willReturn(
                array(
                    'incoming' => 'reject',
                    'routed' => 'reject',
                )
            )
        ;

        $this->assertSame(
            array(
                'incoming' => 'reject',
                'outgoing' => 'allow',
                'routed' => 'reject',

            ),
            $this->firewall->getPolicyByHostname('some-host')
        );
    }
}
