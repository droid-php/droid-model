<?php

namespace Droid\Model\Project;

use RuntimeException;

class Task
{
    private $arguments = [];
    private $commandName;
    private $host_filter;
    private $hosts;
    private $itemFilter;
    private $items = [];
    private $name;
    private $triggers = [];
    private $type = 'task';

    protected $changed = false;

    public function setArgument($name, $value)
    {
        $this->arguments[$name] = $value;
    }

    public function getArgument($name)
    {
        if (!$this->hasArgument($name)) {
            throw new RuntimeException('No such argument');
        }
        return $this->arguments[$name];
    }

    public function hasArgument($name)
    {
        return isset($this->arguments[$name]);
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getCommandName()
    {
        return $this->commandName;
    }

    public function setCommandName($commandName)
    {
        if (!strpos($commandName, ':')) {
            throw new RuntimeException("Invalid command-name: " . $commandName);
        }

        $this->commandName = $commandName;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get an expression by which to select a subset of Task.items.
     *
     * @return string
     */
    public function getItemFilter()
    {
        return $this->itemFilter;
    }

    /**
     * Set an expression by which to select a subset of Task.items.
     *
     * @param string $filterExpression
     */
    public function setItemFilter($filterExpression)
    {
        $this->itemFilter = $filterExpression;
        return $this;
    }

    public function setItems($items)
    {
        $this->items = $items;
    }

    public function getItems()
    {
        if (count($this->items)==0) {
            return [
                'default'
            ];
        }
        return $this->items;
    }

    public function getHosts()
    {
        return $this->hosts;
    }

    public function setHosts($hosts)
    {
        $this->hosts = $hosts;
        return $this;
    }

    public function addTrigger($name)
    {
        $this->triggers[$name] = $name;
    }

    public function getTriggers()
    {
        return $this->triggers;
    }

    public function getChanged()
    {
        return $this->changed;
    }

    public function setChanged($changed)
    {
        $this->changed = $changed;
        return $this;
    }

    /**
     * Get an expression to use to select a subset of some list of hosts on
     * which to run the task.
     *
     * @return string
     */
    public function getHostFilter()
    {
        return $this->host_filter;
    }

    /**
     * Set an expression to use to select a subset of some list of hosts on
     * which to run the task.
     *
     * @param string $filterExpression
     */
    public function setHostFilter($filterExpression)
    {
        $this->host_filter = $filterExpression;
        return $this;
    }
}
