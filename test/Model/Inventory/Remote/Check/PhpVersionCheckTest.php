<?php

namespace Droid\Test\Model\Inventory\Remote;

use Psr\Log\LoggerInterface;
use SSHClient\Client\ClientInterface;

use Droid\Model\Inventory\Remote\AbleInterface;
use Droid\Model\Inventory\Remote\Check\PhpVersionCheck;

class PhpVersionCheckTest extends \PHPUnit_Framework_TestCase
{
    protected $check;
    protected $host;
    protected $logger;
    protected $sshClient;

    public function setUp()
    {
        $this->host = $this
            ->getMockBuilder(AbleInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
        $this->logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->getMock()
        ;
        $this->sshClient = $this
            ->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->check = new PhpVersionCheck;
        $this->check->setLogger($this->logger);
    }

    /**
     * @expectedException \Droid\Model\Inventory\Remote\Check\UnrecoverableCheckFailureException
     * @expectedExceptionMessage Unable to check the version of PHP installed
     */
    public function testCheckWithSshExecFailureWillThrowException()
    {
        $this
            ->host
            ->expects($this->once())
            ->method('getSshClient')
            ->willReturn($this->sshClient)
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
            ->willReturn(1)
        ;
        $this
            ->sshClient
            ->expects($this->once())
            ->method('getErrorOutput')
            ->willReturn('fail!')
        ;

        $this->check->check($this->host);
    }

    /**
     * @expectedException \Droid\Model\Inventory\Remote\Check\UnrecoverableCheckFailureException
     * @expectedExceptionMessage The remotely installed version of PHP is too low
     */
    public function testCheckWithInsufficientPhpVersionWillThrowException()
    {
        $this->check->configure(array('min_php_version' => 50335));

        $this
            ->host
            ->method('getSshClient')
            ->willReturn($this->sshClient)
        ;
        $this
            ->sshClient
            ->method('getExitCode')
            ->willReturn(0)
        ;
        $this
            ->sshClient
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn("50334\n")
        ;

        $this->check->check($this->host);
    }

    public function testCheckWithSufficientPhpVersionWillPass()
    {
        $this->check->configure(array('min_php_version' => 50334));

        $this
            ->host
            ->method('getSshClient')
            ->willReturn($this->sshClient)
        ;
        $this
            ->sshClient
            ->method('getExitCode')
            ->willReturn(0)
        ;
        $this
            ->sshClient
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn("50334\n")
        ;

        $this->assertTrue(
            $this->check->check($this->host),
            'The check passes and boolean true is returned by check().'
        );
    }

    public function testCheckWithHihgerPhpVersionWillPass()
    {
        $this->check->configure(array('min_php_version' => 50333));

        $this
            ->host
            ->method('getSshClient')
            ->willReturn($this->sshClient)
        ;
        $this
            ->sshClient
            ->method('getExitCode')
            ->willReturn(0)
        ;
        $this
            ->sshClient
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn("50334\n")
        ;

        $this->assertTrue(
            $this->check->check($this->host),
            'The check passes and boolean true is returned by check().'
        );
    }
}
