<?php

namespace Droid\Model\Project;

use RuntimeException;

trait ModuleTrait
{
    private $modules = [];
    public function addModule(Module $module)
    {
        $this->modules[$module->getName()] = $module;
    }

    public function getModules()
    {
        return $this->modules;
    }

    public function hasModule($name)
    {
        return isset($this->modules[$name]);
    }

    public function getModule($name)
    {
        if (!$this->hasModule($name)) {
            throw new RuntimeException("No such module: " . $name);
        }
        return $this->modules[$name];
    }
}
