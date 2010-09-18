<?php

namespace Bundle\DoctrineUserBundle\Tests;

use Bundle\DoctrineUserBundle\Auth;
use Bundle\DoctrineUserBundle\DAO\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Session;

class MockUserRepository implements UserRepositoryInterface
{
    public function findOneByUsername($username)
    {}

    public function findOneByEmail($email)
    {}

    public function findOneByUsernameOrEmail($usernameOrEmail)
    {}

    public function findOneByConfirmationToken($token)
    {}

    public function getObjectManager()
    {}

    public function getObjectClass()
    {}

    public function getObjectIdentifier()
    {}
}

class MockSession extends Session
{
    public function __construct()
    {}

    public function start()
    {}
}

class MockAuth extends Auth
{
    public function getOptions()
    {
        return $this->options;
    }
}


class AuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Bundle\DoctrineUserBundle\Auth::__construct
     */
    public function testConstructor()
    {
        $auth = new MockAuth(new MockUserRepository(), new MockSession(), array('test' => 'unit'));
        $options = $auth->getOptions();
        $this->assertEquals('unit', $options['test'], '->__construct() takes an array of parameters as its third argument');

        $auth = new MockAuth(new MockUserRepository(), new MockSession(), array('session_path' => 'test/unit'));
        $options = $auth->getOptions();
        $this->assertEquals('test/unit', $options['session_path'], '->__construct() allows to customize session_path option');

    }
}
