<?php

namespace Droid\Model\Project;

use RuntimeException;

class Project
{
    use VariableTrait;
    use ModuleTrait;

    /**
     * The name of the Project.
     *
     * @var string
     */
    public $name;
    /**
     * A description of the Project.
     *
     * @var string
     */
    public $description;

    private $targets = [];
    private $configFilePath;

    public function __construct($filename)
    {
        if (!file_exists($filename)) {
            throw new RuntimeException("Droid project file not found: " . $filename);
        }
        $this->configFilePath = $filename;
    }

    public function getConfigFilePath()
    {
        return $this->configFilePath;
    }

    public function getBasePath()
    {
        return dirname($this->configFilePath);
    }

    public function addTarget(Target $target)
    {
        $this->targets[] = $target;
    }

    public function getTargets()
    {
        return $this->targets;
    }

    public function getTargetByName($targetName)
    {
        foreach ($this->targets as $target) {
            if ($target->getName() == $targetName) {
                return $target;
            }
        }
        return null;
    }
}
