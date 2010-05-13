<?php

namespace Bundle\DoctrineUserBundle\Tests\Entities;

use Bundle\DoctrineUserBundle\Entities\User;

// Custom Doctrine helper
require_once __DIR__ . '/Helper.php';

require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/../../Entities/UserRepository.php';
require_once __DIR__ . '/../../Entities/User.php';

class UserRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function testFindOneByUsername()
    {
        $tony = new User();
        $tony->setUserName('tony');
        $tony->setPassword('changeme');

        $this->em->persist($tony);
        $this->em->flush();

        $this->assertSame($tony, $this->getRepository()->findOneByUsername('tony'));

        $this->assertNull($this->getRepository()->findOneByUsername('thisusernameisprobablynottakenyet'));
    }

    public function testFindOneByUsernameAndPassword()
    {
        $tony = new User();
        $tony->setUserName('tony');
        $tony->setPassword('changeme');

        $this->em->persist($tony);
        $this->em->flush();

        $this->assertSame($tony, $this->getRepository()->findOneByUsernameAndPassword('tony', 'changeme'));

        $this->assertNull($this->getRepository()->findOneByUsernameAndPassword('thisusernameisprobablynottakenyet', 'badpassword'));
        $this->assertNull($this->getRepository()->findOneByUsernameAndPassword('thisusernameisprobablynottakenyet', 'changeme'));
        $this->assertNull($this->getRepository()->findOneByUsernameAndPassword('tony', 'badpassword'));
        $this->assertNull($this->getRepository()->findOneByUsernameAndPassword('tony', ''));
    }

    public function testUsername()
    {
        $user = new User();
        $this->assertNull($user->getUsername());
        
        $user->setUsername('tony');
        $this->assertEquals('tony', $user->getUsername());
    }

    /**
     * @return Bundle\DoctrineUserBundle\Entities\UserRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository('Bundle\DoctrineUserBundle\Entities\User');
    }

    public function setup()
    {
        $this->setupOrm();
    }

    protected function setupOrm()
    {
        $helper = new Helper();

        $this->em = $helper->createEntityManager();

        $helper->dropAndCreate($this->em, array(
            'Bundle\\DoctrineUserBundle\\Entities\\User'
        ));
    }

}