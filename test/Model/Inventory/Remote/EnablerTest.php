<?php

namespace Droid\Test\Model\Inventory\Remote;

use Psr\Log\LoggerInterface;

use Droid\Model\Inventory\Remote\AbleInterface;
use Droid\Model\Inventory\Remote\AbstractSynchroniser;
use Droid\Model\Inventory\Remote\Check\AbstractHostCheck;
use Droid\Model\Inventory\Remote\Check\CheckFailureException;
use Droid\Model\Inventory\Remote\Check\UnrecoverableCheckFailureException;
use Droid\Model\Inventory\Remote\Enabler;
use Droid\Model\Inventory\Remote\SynchronisationException;

class EnablerTest extends \PHPUnit_Framework_TestCase
{
    protected $check;
    protected $enabler;
    protected $host;
    protected $logger;
    protected $synchroniser;

    public function setUp()
    {
        $this->synchroniser = $this
            ->getMockBuilder(AbstractSynchroniser::class)
            ->getMock()
        ;
        $this->host = $this
            ->getMockBuilder(AbleInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->check = $this
            ->getMockBuilder(AbstractHostCheck::class)
            ->getMock()
        ;
        $this->logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->getMock()
        ;
        $this->enabler = new Enabler($this->synchroniser);
        $this->enabler->setLogger($this->logger);
        $this->enabler->addHostCheck($this->check);
    }

    /**
     * @expectedException \Droid\Model\Inventory\Remote\EnablementException
     * @expectedExceptionMessage Failure during host check
     */
    public function testEnableWithUnrecoverableCheckFailureWillThrowException()
    {
        $this
            ->host
            ->expects($this->once())
            ->method('unable')
        ;
        $this
            ->host
            ->method('getName')
            ->willReturn('test_host')
        ;
        $this
            ->check
            ->expects($this->once())
            ->method('check')
            ->with($this->equalTo($this->host))
            ->willThrowException(new UnrecoverableCheckFailureException)
        ;
        $this
            ->host
            ->expects($this->never())
            ->method('able')
        ;

        $this->enabler->enable($this->host);
    }

    public function testEnableWithCheckFailureWillContinueEnablement()
    {
        $this
            ->host
            ->expects($this->once())
            ->method('unable')
        ;
        $this
            ->host
            ->method('getName')
            ->willReturn('test_host')
        ;
        $this
            ->check
            ->expects($this->once())
            ->method('check')
            ->with($this->equalTo($this->host))
            ->willThrowException(new CheckFailureException)
        ;
        $this
            ->synchroniser
            ->expects($this->once())
            ->method('sync')
            ->with($this->equalTo($this->host))
        ;
        $this
            ->host
            ->expects($this->once())
            ->method('able')
        ;

        $this->enabler->enable($this->host);
    }

    public function testEnableWithPassedCheckWillContinueEnablement()
    {
        $this
            ->host
            ->expects($this->once())
            ->method('unable')
        ;
        $this
            ->host
            ->method('getName')
            ->willReturn('test_host')
        ;
        $this
            ->check
            ->expects($this->once())
            ->method('check')
            ->with($this->equalTo($this->host))
        ;
        $this
            ->synchroniser
            ->expects($this->once())
            ->method('sync')
            ->with($this->equalTo($this->host))
        ;
        $this
            ->host
            ->expects($this->once())
            ->method('able')
        ;

        $this->enabler->enable($this->host);
    }

    public function testEnableWithLoggerAwareCheckWillInjectLoggerIntoCheck()
    {
        $this
            ->check
            ->expects($this->once())
            ->method('setLogger')
            ->with($this->equalTo($this->logger))
        ;

        $this->enabler->enable($this->host);
    }

    public function testEnableWithLoggerAwareSynchroniserWillInjectLoggerIntoSynchroniser()
    {
        $this
            ->synchroniser
            ->expects($this->once())
            ->method('setLogger')
            ->with($this->equalTo($this->logger))
        ;

        $this->enabler->enable($this->host);
    }

    /**
     * @expectedException \Droid\Model\Inventory\Remote\EnablementException
     * @expectedExceptionMessage Failure during binary synchronisation
     */
    public function testEnableWithFailedSynchronisationWillThrowException()
    {
        $this
            ->host
            ->method('getName')
            ->willReturn('test_host')
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
}
