<?php

namespace Droid\Test\Model\Inventory;

use PHPUnit_Framework_TestCase;

use Droid\Model\Inventory\Host;

class HostTest extends PHPUnit_Framework_TestCase
{
    public function testGetConnectionIpWithDroidPublicAndPrivateIpWillReturnDroidIp()
    {
        $host = new Host('some-host');
        $host->droid_ip = '192.0.2.0';
        $host->public_ip = '192.0.2.1';
        $host->private_ip = '192.0.2.2';

        $this->assertSame('192.0.2.0', $host->getConnectionIp());
    }

    public function testGetConnectionIpWithPublicAndPrivateIpWillReturnPublicIp()
    {
        $host = new Host('some-host');
        $host->droid_ip = null;
        $host->public_ip = '192.0.2.1';
        $host->private_ip = '192.0.2.2';

        $this->assertSame('192.0.2.1', $host->getConnectionIp());
    }

    public function testGetConnectionIpWithPrivateIpWillReturnPrivateIp()
    {
        $host = new Host('some-host');
        $host->droid_ip = null;
        $host->public_ip = null;
        $host->private_ip = '192.0.2.2';

        $this->assertSame('192.0.2.2', $host->getConnectionIp());
    }

    public function testGetConnectionPortWithDroidPortWillReturnDroidPort()
    {
        $host = new Host('some-host');
        $host->droid_port = 2222;

        $this->assertSame(2222, $host->getConnectionPort());
    }

    public function testGetConnectionPortWithoutDroidPortWillReturnStandardSshPort()
    {
        $host = new Host('some-host');
        $host->droid_port = null;

        $this->assertSame(22, $host->getConnectionPort());
    }
}
