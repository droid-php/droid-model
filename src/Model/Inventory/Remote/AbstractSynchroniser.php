<?php

namespace Droid\Model\Inventory\Remote;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class AbstractSynchroniser implements
    LoggerAwareInterface,
    SynchroniserInterface
{
    use LoggerAwareTrait;

    abstract public function sync(AbleInterface $host);
}
