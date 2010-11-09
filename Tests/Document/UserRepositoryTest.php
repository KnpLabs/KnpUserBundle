<?php

namespace Bundle\DoctrineUserBundle\Document;

class UserRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;

    public function testFindOneByUsername()
    {
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('usernameLower' => 'jack')));

        $this->repo->findOneByUsername('jack');
    }

    public function testFindOneByUsernameLowercasesTheUsername()
    {
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('usernameLower' => 'jack')));

        $this->repo->findOneByUsername('JaCk');
    }

    public function testFindOneByEmail()
    {
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('email' => 'jack@email.org')));

        $this->repo->findOneByEmail('jack@email.org');
    }

    public function testFindOneByEmailLowercasesTheEmail()
    {
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('email' => 'jack@email.org')));

        $this->repo->findOneByEmail('JaCk@EmAiL.oRg');
    }

    public function testFindOneByUsernameOrEmailWithUsername()
    {
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('usernameLower' => 'jack')));

        $this->repo->findOneByUsernameOrEmail('JaCk');
    }

    public function testFindOneByUsernameOrEmailWithEmail()
    {
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('email' => 'jack@email.org')));

        $this->repo->findOneByUsernameOrEmail('JaCk@EmAiL.oRg');
    }

    public function testLoadUserByUsernameWithExistingUser()
    {
        $userMock = $this->getMock('Bundle\DoctrineUserBundle\Document\User');

        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('usernameLower' => 'jack')))
            ->will($this->returnValue($userMock));

        $this->repo->loadUserByUsername('jack');
    }

    /**
     * @expectedException Symfony\Component\Security\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameWithMissingUser()
    {
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('usernameLower' => 'jack')))
            ->will($this->returnValue(null));

        $this->repo->loadUserByUsername('jack');
    }

    public function setUp()
    {
        $this->repo = $this->getRepositoryMock();
    }

    public function tearDown()
    {
        unset($this->repo);
    }

    protected function getRepositoryMock()
    {
        $methods = array('findOneBy');
        return $this->getMock('Bundle\DoctrineUserBundle\Document\UserRepository', $methods, array(), '', false);
    }
}
