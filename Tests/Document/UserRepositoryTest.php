<?php

namespace Bundle\FOS\UserBundle\Document;

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
        $userMock = $this->getMock('Bundle\FOS\UserBundle\Document\User', array(), array('sha1'));

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
        if (!class_exists('\Doctrine\ODM\MongoDB\DocumentManager')) {
            $this->markTestSkipped('No ODM installed');
        }

        $this->repo = $this->getRepositoryMock();
    }

    public function tearDown()
    {
        unset($this->repo);
    }

    protected function getRepositoryMock()
    {
        $methods = array('findOneBy');
        $repo = $this->getMock('Bundle\FOS\UserBundle\Document\UserRepository', $methods, array(), '', false);

        return $repo;
    }
}
