<?php

namespace Droid\Model\Project;

class TargetArgument
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    private $description;
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    private $required = false;
    
    public function getRequired()
    {
        return $this->required;
    }
    
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }
}
