<?php

namespace Droid\Model\Inventory;

use Droid\Model\Feature\Firewall\RuleTrait;
use Droid\Model\Inventory\Remote\AbleInterface;
use Droid\Model\Inventory\Remote\AbleTrait;
use Droid\Model\Inventory\Remote\SshClientTrait;
use Droid\Model\Project\VariableTrait;

class Host implements AbleInterface
{
    public $name;
    public $address;
    public $public_ip;
    public $private_ip;
    public $public_port;
    public $private_port;

    private $username;
    private $password;
    private $keyFile;
    private $keyPass;
    private $auth;

    use AbleTrait;
    use RuleTrait;
    use SshClientTrait;
    use VariableTrait;

    public function __construct($name)
    {
        $this->name = $name;
        $this->address = $name;
        $this->public_port = 22;
        $this->private_port = 22;
        $this->username = null;
        $this->auth = 'agent';
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @deprecated Use instead the public property 'address'.
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @deprecated Use instead the public property 'address'.
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @deprecated Use instead the public property 'public_ip'.
     */
    public function getPublicIp()
    {
        return $this->public_ip;
    }

    /**
     * @deprecated Use instead the public property 'public_ip'.
     */
    public function setPublicIp($ip)
    {
        $this->public_ip = $ip;
        return $this;
    }

    /**
     * @deprecated Use instead the public property 'private_ip'.
     */
    public function getPrivateIp()
    {
        return $this->private_ip;
    }

    /**
     * @deprecated Use instead the public property 'private_ip'.
     */
    public function setPrivateIp($ip)
    {
        $this->private_ip = $ip;
        return $this;
    }

    /**
     * @deprecated Use instead the public property 'public_port'.
     */
    public function getPublicPort()
    {
        return $this->public_port;
    }

    /**
     * @deprecated Use instead the public property 'public_port'.
     */
    public function setPublicPort($port)
    {
        $this->public_port = $port;
        return $this;
    }

    /**
     * @deprecated Use instead the public property 'private_port'.
     */
    public function getPrivatePort()
    {
        return $this->private_port;
    }

    /**
     * @deprecated Use instead the public property 'private_port'.
     */
    public function setPrivatePort($port)
    {
        $this->private_port = $port;
        return $this;
    }

    public function getConnectionIp()
    {
        // TODO: Allow to use public or private ip
        return $this->public_ip;
    }

    public function getConnectionPort()
    {
        // TODO: Allow to use public or private port
        return $this->public_port;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public function setAuth($auth)
    {
        $this->auth = $auth;
        return $this;
    }

    public function getKeyFile()
    {
        return $this->keyFile;
    }

    public function setKeyFile($keyFile)
    {
        $this->keyFile = $keyFile;
        return $this;
    }

    public function getKeyPass()
    {
        return $this->keyPass;
    }

    public function setKeyPass($keyPass)
    {
        $this->keyPass = $keyPass;
        return $this;
    }
}
