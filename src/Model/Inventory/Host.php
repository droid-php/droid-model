<?php

namespace Droid\Model\Inventory;

use Droid\Model\Feature\Firewall\PolicyTrait;
use Droid\Model\Feature\Firewall\RuleTrait;
use Droid\Model\Inventory\Remote\AbleInterface;
use Droid\Model\Inventory\Remote\AbleTrait;
use Droid\Model\Inventory\Remote\SshClientTrait;
use Droid\Model\Project\VariableTrait;

class Host implements AbleInterface
{
    public $name;
    public $address;
    /**
     * The IP address by which Droid will connect to this host.
     *
     * @var string
     */
    public $droid_ip;
    /**
     * The port number by which Droid will connect to this host.
     *
     * @var number
     */
    public $droid_port;
    public $public_ip;
    public $private_ip;

    private $username;
    private $keyFile;

    use AbleTrait;
    use PolicyTrait;
    use RuleTrait;
    use SshClientTrait;
    use VariableTrait;

    public function __construct($name)
    {
        $this->name = $name;
        $this->address = $name;
        $this->username = null;
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

    public function getConnectionIp()
    {
        if ($this->droid_ip) {
            return $this->droid_ip;
        } elseif ($this->public_ip) {
            return $this->public_ip;
        }
        return $this->private_ip;
    }

    public function getConnectionPort()
    {
        return $this->droid_port ?: 22;
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

    public function getKeyFile()
    {
        return $this->keyFile;
    }

    public function setKeyFile($keyFile)
    {
        $this->keyFile = $keyFile;
        return $this;
    }
}
