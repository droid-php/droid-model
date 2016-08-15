<?php

namespace Droid\Test\Model\Feature\Firewall;

use Droid\Model\Feature\Firewall\Firewall;
use Droid\Model\Inventory\Inventory;

class GetPolicyTest extends \PHPUnit_Framework_TestCase
{
    protected $inventory;
    protected $firewall;

    protected function setUp()
    {
        $this->inventory = $this->getMock(Inventory::class);
        $this->firewall =  new Firewall($this->inventory);
    }

    public function testGetPolicyWithoutInventoryPolicyReturnsEmpty()
    {
        $this->assertEmpty($this->firewall->getPolicy());
    }

    public function testGetPolicyWithInventoryPolicyReturnsInventoryPolicy()
    {
        $customPolicy = array('some_policy_key' => 'some_policy_value');

        $this
            ->inventory
            ->expects($this->once())
            ->method('hasVariable')
            ->with('firewall_policy')
            ->willReturn(true)
        ;
        $this
            ->inventory
            ->expects($this->once())
            ->method('getVariable')
            ->with('firewall_policy')
            ->willReturn($customPolicy)
        ;

        $this->assertSame(
            $customPolicy,
            $this->firewall->getPolicy()
        );
    }
}
