<?php

namespace Bundle\DoctrineUserBundle\Tests;

use Bundle\DoctrineUserBundle\Auth;
use Bundle\DoctrineUserBundle\DAO\UserRepositoryInterface;
use Bundle\DoctrineUserBundle\DAO\User;
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

    public function findOneByRememberMeToken($token)
    {}
}

class MockUser extends User
{
    public $permissionNames = array();

    public function getAllPermissionNames()
    {
        return $this->permissionNames;
    }
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
    public $isAuthenticated;

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function isAuthenticated()
    {
        return (bool)$this->isAuthenticated;
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

    /**
     * @covers Bundle\DoctrineUserBundle\Auth::hasCredentials
     */
    public function testHasCredentials()
    {
        $auth = new MockAuth(new MockUserRepository(), new MockSession());
        $auth->isAuthenticated = false;
        $this->assertFalse($auth->hasCredentials('perm1'), '->hasCredentials() returns false if user is not authenticated');

        $auth->isAuthenticated = true;
        $this->assertTrue($auth->hasCredentials(''), '->hasCredentials() returns true if first parameter is an empty string');
        $this->assertTrue($auth->hasCredentials(array()), '->hasCredentials() returns true if first parameter is an array string');

        $user = new MockUser();
        $user->permissionNames = array('perm1', 'perm3', 'perm4');
        $auth->setUser($user);

        $this->assertTrue($auth->hasCredentials('perm1'), '->hasCredentials() returns true if user has permission passed');
        $this->assertFalse($auth->hasCredentials('perm2'), '->hasCredentials() returns false if user has not permission passed');
        $this->assertTrue($auth->hasCredentials(array('perm1', 'perm3'), true), '->hasCredentials() returns true if user has all permissions passed and second parameter is true');

        $this->assertTrue($auth->hasCredentials(array('perm0', 'perm1', 'perm2'), false), '->hasCredentials() returns true if user has at least one of permissions passed and second parameter is false');
        $this->assertFalse($auth->hasCredentials(array('perm0', 'perm2'), false), '->hasCredentials() returns false if user has at no permissions passed and second parameter is false');
    }
}
