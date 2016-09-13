<?php

namespace Droid\Model\Inventory\Remote\Check;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

use Droid\Model\Inventory\Remote\AbleInterface;

abstract class AbstractHostCheck implements
    HostCheckInterface,
    LoggerAwareInterface
{
    use LoggerAwareTrait;

    abstract public function configure(array $options);

    abstract public function check(AbleInterface $host);
}
