<?php

namespace Droid\Model\Project;

use InvalidArgumentException;
use RuntimeException;

class Task
{
    const RUNTIME_MAX = 60;

    private $arguments = [];
    private $commandName;
    private $elevatePrivileges;
    private $host_filter;
    private $hosts;
    private $itemFilter;
    private $items = [];
    private $maxRuntime;
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

    /**
     * Determine whether or not the command associated with this Task should be
     * executed with elevated privileges.
     *
     * @return boolean
     */
    public function getElevatePrivileges()
    {
        return $this->elevatePrivileges === true;
    }

    /**
     * Direct the Task as to whether or not to execute its associated command
     * with elevated privileges.
     *
     * @param boolean $elevatePrivileges
     */
    public function setElevatePrivileges($elevatePrivileges)
    {
        if (! is_bool($elevatePrivileges)) {
            throw new InvalidArgumentException(
                'Expected boolean $elevatePrivileges argument'
            );
        }

        $this->elevatePrivileges = $elevatePrivileges;
        return $this;
    }

    /**
     * Get the maximum runtime, in seconds. A value of zero is meant to be
     * construed as unlimited runtime.
     *
     * @return integer
     */
    public function getMaxRuntime()
    {
        return $this->maxRuntime !== null
            ? $this->maxRuntime
            : self::RUNTIME_MAX
        ;
    }

    /**
     * Set the maximum runtime, in seconds.
     *
     * @param integer $maxRuntime
     */
    public function setMaxRuntime($maxRuntime)
    {
        if (! is_numeric($maxRuntime)) {
            throw new InvalidArgumentException(
                'Expected numeric $$maxRuntime argument'
            );
        }

        $this->maxRuntime = (int) $maxRuntime;
        return $this;
    }
}
