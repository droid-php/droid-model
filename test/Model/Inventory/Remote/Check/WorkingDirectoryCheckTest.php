<?php

namespace Droid\Test\Model\Inventory\Remote;

use Psr\Log\LoggerInterface;
use SSHClient\Client\ClientInterface;

use Droid\Model\Inventory\Remote\AbleInterface;
use Droid\Model\Inventory\Remote\Check\WorkingDirectoryCheck;

class WorkingDirectoryCheckTest extends \PHPUnit_Framework_TestCase
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

        $this->check = new WorkingDirectoryCheck;
        $this->check->setLogger($this->logger);
    }

    public function testCheckWithUnconfiguredPathWillPass()
    {
        $this
            ->host
            ->expects($this->never())
            ->method('getSshClient')
        ;

        $this->assertTrue(
            $this->check->check($this->host),
            'The check passes and boolean true is returned by check().'
        );
    }

    public function testCheckWithSshExecFailureWillReturnFalse()
    {
        $this->check->configure(array('working_dir_path' => 'some/path'));

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
            ->with(
                $this->equalTo(
                    array('(cd some/path)', '&&', 'test -w some/path')
                )
            )
        ;
        $this
            ->sshClient
            ->expects($this->once())
            ->method('getExitCode')
            ->willReturn(1)
        ;

        $this->assertFalse(
            $this->check->check($this->host),
            'The check does not pass and boolean false is returned by check().'
        );
    }

    public function testCheckWithSshExecSuccessWillSetWorkingDir()
    {
        $this->check->configure(array('working_dir_path' => 'some/path'));

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
            ->with(
                $this->equalTo(
                    array('(cd some/path)', '&&', 'test -w some/path')
                )
            )
        ;
        $this
            ->sshClient
            ->expects($this->once())
            ->method('getExitCode')
            ->willReturn(0)
        ;
        $this
            ->host
            ->expects($this->once())
            ->method('setWorkingDirectory')
            ->with($this->equalTo('some/path'))
        ;

        $this->check->check($this->host);
    }

    public function testCheckWithSshExecSuccessWillReturnTrue()
    {
        $this->check->configure(array('working_dir_path' => 'some/path'));

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
            ->with(
                $this->equalTo(
                    array('(cd some/path)', '&&', 'test -w some/path')
                )
            )
        ;
        $this
            ->sshClient
            ->expects($this->once())
            ->method('getExitCode')
            ->willReturn(0)
        ;

        $this->assertTrue(
            $this->check->check($this->host),
            'The check passes and boolean true is returned by check().'
        );
    }
}
