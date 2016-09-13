<?php

namespace Droid\Model\Inventory\Remote\Check;

/**
 * CheckFailureException thrown during the performance of a check and which
 * should be regarded as fatal to continued execution.
 */
class UnrecoverableCheckFailureException extends CheckFailureException
{
}
