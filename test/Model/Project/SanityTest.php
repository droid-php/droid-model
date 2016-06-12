<?php

namespace Droid\Test\Model\Project;

use Droid\Model\Project\Module;
use Droid\Model\Project\Project;
use Droid\Model\Project\RegisteredCommand;
use Droid\Model\Project\Target;
use Droid\Model\Project\Task;

class SanityTest extends \PHPUnit_Framework_TestCase
{
    public function testICanLoadModule()
    {
        new Module('some-mod-name', 'path/to/module');
    }

    public function testICanLoadProject()
    {
        new Project(__FILE__); # the arg only needs to be an existing file
    }

    public function testICanLoadRegisteredCommand()
    {
        new RegisteredCommand('SomeClassName');
    }

    public function testICanLoadTarget()
    {
        new Target('some-target-name');
    }

    public function testICanLoadTask()
    {
        new Task;
    }
}
