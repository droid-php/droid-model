<?php

namespace Droid\Model\Project;

trait VariableTrait
{
    public $variables = [];

    public function setVariable($name, $value)
    {
        $this->variables[$name] = $value;
        return $this;
    }

    public function hasVariable($name)
    {
        return isset($this->variables[$name]);
    }

    public function getVariable($name)
    {
        if (!$this->hasVariable($name)) {
            throw new RuntimeException("No such variable: " . $name);
        }
        return $this->variables[$name];
    }

    public function getVariables()
    {
        return $this->variables;
    }

    public function getVariablesAsString()
    {
        $pairs = array();
        foreach ($this->variables as $name => $value) {
            $pairs[] = sprintf(
                '%s=`%s`',
                $name,
                is_array($value) ? '{...}' : $value
            );
        }
        return implode(' ', $pairs);
    }
}
