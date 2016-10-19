<?php

namespace Droid\Model\Project;

trait EnvironmentAwareTrait
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return \Droid\Model\Project\Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}
