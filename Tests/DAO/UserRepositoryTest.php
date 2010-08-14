<?php

namespace Bundle\DoctrineUserBundle\Tests\DAO;

use Bundle\DoctrineUserBundle\DAO\User;
use Bundle\DoctrineUserBundle\DAO\UserRepository;

// Kernel creation required namespaces
use Symfony\Components\Finder\Finder;

class UserRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUserRepo()
    {
        $userRepo = $this->createKernel()->getContainer()->getDoctrineUser_UserRepoService();
        $this->assertTrue($userRepo instanceof UserRepository);

        return $userRepo;
    }

    /**
     * @depends testGetUserRepo
     */
    public function testCreateNewUser(UserRepository $userRepo)
    {
        $userClass = $userRepo->getObjectClass();
        $user = new $userClass();
        $user->setUserName('harry');
        $user->setEmail('harry@mail.org');
        $user->setPassword('changeme');

        $objectManager = $userRepo->getObjectManager();
        $objectManager->persist($user);
        $objectManager->flush();

        $this->assertNotNull($user->getId());

        return array($userRepo, $user);
    }

    /**
     * @depends testCreateNewUser
     */
    public function testTimestampable(array $dependencies)
    {
        list($userRepo, $user) = $dependencies;
        
        $this->assertTrue($user->getCreatedAt() instanceof \DateTime);
        $this->assertEquals(new \DateTime(), $user->getCreatedAt());
        
        $this->assertTrue($user->getUpdatedAt() instanceof \DateTime);
        $this->assertEquals(new \DateTime(), $user->getUpdatedAt());
    }

    /**
     * @depends testCreateNewUser
     */
    public function testFindOneById(array $dependencies)
    {
        list($userRepo, $user) = $dependencies;

        $fetchedUser = $userRepo->findOneById($user->getId());
        $this->assertSame($user, $fetchedUser);

        $nullUser = $userRepo->findOneById(0);
        $this->assertNull($nullUser);
    }

    /**
     * @depends testCreateNewUser
     */
    public function testFindOneByUsername(array $dependencies)
    {
        list($userRepo, $user) = $dependencies;

        $fetchedUser = $userRepo->findOneByUsername($user->getUsername());
        $this->assertEquals($user->getUsername(), $fetchedUser->getUsername());

        $nullUser = $userRepo->findOneByUsername('thisusernamedoesnotexist----thatsprettyobivous');
        $this->assertNull($nullUser);
    }

    /**
     * @depends testCreateNewUser
     */
    public function testFindOneByEmail(array $dependencies)
    {
        list($userRepo, $user) = $dependencies;

        $fetchedUser = $userRepo->findOneByEmail($user->getEmail());
        $this->assertEquals($user->getEmail(), $fetchedUser->getEmail());

        $nullUser = $userRepo->findOneByEmail('thisemaildoesnotexist----thatsprettyobivous');
        $this->assertNull($nullUser);
    }

    /**
     * @depends testCreateNewUser
     */
    public function testFindOneByUsernameOrEmail(array $dependencies)
    {
        list($userRepo, $user) = $dependencies;

        $userClass = $userRepo->getObjectClass();
        $user2 = new $userClass();
        $user2->setUserName('harry2');
        $user2->setEmail('harry2@mail.org');
        $user2->setPassword('changeme2');

        $objectManager = $userRepo->getObjectManager();
        $objectManager->persist($user2);
        $objectManager->flush();

        $fetchedUser = $userRepo->findOneByUsernameOrEmail($user->getUsername());
        $this->assertEquals($user->getUsername(), $fetchedUser->getUsername());

        $fetchedUser = $userRepo->findOneByUsernameOrEmail($user2->getUsername());
        $this->assertEquals($user2->getUsername(), $fetchedUser->getUsername());

        $fetchedUser = $userRepo->findOneByUsernameOrEmail($user->getEmail());
        $this->assertEquals($user->getEmail(), $fetchedUser->getEmail());

        $fetchedUser = $userRepo->findOneByUsernameOrEmail($user2->getEmail());
        $this->assertEquals($user2->getEmail(), $fetchedUser->getEmail());

        $nullUser = $userRepo->findOneByUsernameOrEmail('thisemaildoesnotexist----thatsprettyobivous');
        $this->assertNull($nullUser);
    }

    //public function testFindOneByUsernameAndPassword()
    //{
        //$tony = new User();
        //$tony->setUserName('tony');
        //$tony->setPassword('changeme');

        //$this->em->persist($tony);
        //$this->em->flush();

        //$this->assertSame($tony, $this->getRepository()->findOneByUsernameAndPassword('tony', 'changeme'));

        //$this->assertNull($this->getRepository()->findOneByUsernameAndPassword('thisusernameisprobablynottakenyet', 'badpassword'));
        //$this->assertNull($this->getRepository()->findOneByUsernameAndPassword('thisusernameisprobablynottakenyet', 'changeme'));
        //$this->assertNull($this->getRepository()->findOneByUsernameAndPassword('tony', 'badpassword'));
        //$this->assertNull($this->getRepository()->findOneByUsernameAndPassword('tony', ''));
    //}

    /**
     * Creates a Kernel.
     *
     * If you run tests with the PHPUnit CLI tool, everything will work as expected.
     * If not, override this method in your test classes.
     *
     * Available options:
     *
     *  * environment
     *  * debug
     *
     * @param array $options An array of options
     *
     * @return HttpKernelInterface A HttpKernelInterface instance
     */
    protected function createKernel(array $options = array())
    {
        // black magic below, you have been warned!
        $dir = getcwd();
        if (!isset($_SERVER['argv']) || false === strpos($_SERVER['argv'][0], 'phpunit')) {
            throw new \RuntimeException('You must override the WebTestCase::createKernel() method.');
        }

        // find the --configuration flag from PHPUnit
        $cli = implode(' ', $_SERVER['argv']);
        if (preg_match('/\-\-configuration[= ]+([^ ]+)/', $cli, $matches)) {
            $dir = $dir.'/'.$matches[1];
        } elseif (preg_match('/\-c +([^ ]+)/', $cli, $matches)) {
            $dir = $dir.'/'.$matches[1];
        } else {
            throw new \RuntimeException('Unable to guess the Kernel directory.');
        }

        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }

        $finder = new Finder();
        $finder->name('*Kernel.php')->in($dir);
        if (!count($finder)) {
            throw new \RuntimeException('You must override the WebTestCase::createKernel() method.');
        }

        $file = current(iterator_to_array($finder));
        $class = $file->getBasename('.php');
        unset($finder);

        require_once $file;

        $kernel = new $class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $debug : true
        );
        $kernel->boot();

        return $kernel;
    }
}
