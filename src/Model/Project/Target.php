<?php

namespace Droid\Model\Project;

use RuntimeException;

class Target
{
    private $name;
    private $arguments = [];

    use VariableTrait;
    use TaskTrait;
    use ModuleTrait;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    private $hosts;

    public function getHosts()
    {
        return $this->hosts;
    }

    public function setHosts($hosts)
    {
        $this->hosts = $hosts;
        return $this;
    }
    
    public function addArgument(TargetArgument $targetArgument)
    {
        $this->arguments[$targetArgument->getName()] = $targetArgument;
        return $this;
    }
    
    public function getArguments()
    {
        return $this->arguments;
    }
    
    public function hasArgument($name)
    {
        return isset($this->arguments[$name]);
    }
    
    public function getArgument($name)
    {
        if (!$this->hasArgument($name)) {
            throw new RuntimeException("No such argument: " . $name);
        }
        return $this->arguments[$name];
    }
}
