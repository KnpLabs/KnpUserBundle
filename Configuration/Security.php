<?php

namespace Bundle\DoctrineUserBundle\Configuration;

use Bundle\Sensio\FrameworkExtraBundle\Configuration\ConfigurationInterface;

class Security implements ConfigurationInterface
{
    protected $isSecure;
    protected $credentials;

    public function setIsSecure($isSecure)
    {
        $this->isSecure = $isSecure;
    }

    public function getIsSecure()
    {
        return $this->isSecure;
    }

    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function getAliasName()
    {
        return 'security';
    }

}