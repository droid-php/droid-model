<?php

namespace Droid\Test\Model\Inventory\Remote;

use Droid\Model\Inventory\Remote\AbleInterface;
use Droid\Model\Inventory\Remote\Enabler;
use Droid\Model\Inventory\Remote\SynchronisationException;
use Droid\Model\Inventory\Remote\SynchroniserInterface;

use SSHClient\Client\ClientInterface;

class EnablerTest extends \PHPUnit_Framework_TestCase
{
    protected $enabler;
    protected $synchroniser;
    protected $host;
    protected $sshClient;
    protected $scpClient;

    public function setUp()
    {
        $this->synchroniser = $this
            ->getMockBuilder(SynchroniserInterface::class)
            ->setConstructorArgs(array('some_path'))
            ->getMock()
        ;
        $this->host = $this
            ->getMockBuilder(AbleInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
        $this->sshClient = $this
            ->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->scpClient = $this
            ->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->enabler = new Enabler($this->synchroniser);
    }

    /**
     * @expectedException \Droid\Model\Inventory\Remote\EnablementException
     * @expectedExceptionMessage Unable to check remote PHP version
     */
    public function testEnableFailsWhenSshExecFails()
    {
        $this
            ->host
            ->expects($this->once())
            ->method('unable')
        ;
        $this
            ->host
            ->expects($this->once())
            ->method('getSshClient')
            ->willReturn($this->sshClient)
        ;
        $this
            ->host
            ->expects($this->once())
            ->method('getName')
            ->willReturn('test_host')
        ;
        $this
            ->sshClient
            ->expects($this->once())
            ->method('exec')
            ->with($this->equalTo(array('php', '-r', '"echo PHP_VERSION_ID;"')))
        ;
        $this
            ->sshClient
            ->expects($this->once())
            ->method('getExitCode')
            ->willReturn(1) # exec failed
        ;
        $this
            ->host
            ->expects($this->never())
            ->method('able')
        ;

        $this->enabler->enable($this->host);
    }

    /**
     * @expectedException \Droid\Model\Inventory\Remote\EnablementException
     * @expectedExceptionMessage version of PHP is too low
     */
    public function testEnableFailsWhenPhpVersionTooLow()
    {
        $this
            ->host
            ->method('getSshClient')
            ->willReturn($this->sshClient)
        ;
        $this
            ->host
            ->method('getName')
            ->willReturn('test_host')
        ;
        $this
            ->sshClient
            ->method('getExitCode')
            ->willReturn(0) # exec ok
        ;
        $this
            ->sshClient
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn("50334\n")
        ;
        $this
            ->host
            ->expects($this->never())
            ->method('able')
        ;

        $this->enabler->enable($this->host);
    }

    /**
     * @expectedException \Droid\Model\Inventory\Remote\EnablementException
     * @expectedExceptionMessage Failure during binary synchronisation
     */
    public function testEnableFailsWhenSynchronisationFails()
    {
        $this
            ->host
            ->method('getSshClient')
            ->willReturn($this->sshClient)
        ;
        $this
            ->host
            ->method('getName')
            ->willReturn('test_host')
        ;
        $this
            ->sshClient
            ->method('getExitCode')
            ->willReturn(0) # exec ok
        ;
        $this
            ->sshClient
            ->method('getOutput')
            ->willReturn("50509\n")
        ;
        $this
            ->synchroniser
            ->expects($this->once())
            ->method('sync')
            ->with($this->host)
            ->willThrowException(
                new SynchronisationException('test_host', 'test_message')
            )
        ;
        $this
            ->host
            ->expects($this->never())
            ->method('able')
        ;

        $this->enabler->enable($this->host);
    }

    public function testEnableSucceeds()
    {
        $this
            ->host
            ->method('getSshClient')
            ->willReturn($this->sshClient)
        ;
        $this
            ->host
            ->method('getName')
            ->willReturn('test_host')
        ;
        $this
            ->sshClient
            ->method('getExitCode')
            ->willReturn(0) # exec ok
        ;
        $this
            ->sshClient
            ->method('getOutput')
            ->willReturn("50509\n")
        ;
        $this
            ->synchroniser
            ->method('sync')
        ;
        $this
            ->host
            ->expects($this->once())
            ->method('able')
        ;

        $this->enabler->enable($this->host);
    }
}
