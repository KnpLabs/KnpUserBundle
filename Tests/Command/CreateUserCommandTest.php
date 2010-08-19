<?php

namespace Bundle\DoctrineUserBundle\Tests\Command;

use Bundle\DoctrineUserBundle\DAO\User;
use Bundle\DoctrineUserBundle\Command\CreateUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Components\Console\Output\Output;
use Symfony\Components\Console\Tester\ApplicationTester;

// Kernel creation required namespaces
use Symfony\Components\Finder\Finder;

class CreateUserCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testUserCreation()
    {
        $kernel = self::createKernel();
        $command = new CreateUserCommand();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);
        $username = 'test_username';
        $password = 'test_password';
        $email    = 'test_email@email.org';
        $tester->run(array(
            'command'  => $command->getFullName(),
            'username' => $username,
            'password' => $password,
            'email'    => $email,
        ), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));

        $kernel = self::createKernel();
        $userRepo = $kernel->getContainer()->get('doctrine_user.user_repository');
        $user = $userRepo->findOneByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertTrue($user->checkPassword($password));
        $this->assertEquals($email, $user->getEmail());

        $userRepo->getObjectManager()->remove($user);
        $userRepo->getObjectManager()->flush();
    }

    static public function tearDownAfterClass()
    {
        $userRepo = self::createKernel()->getContainer()->getDoctrineUser_UserRepositoryService();
        $objectManager = $userRepo->getObjectManager();
        if($object = $userRepo->findOneByUsername('test_username')) {
            $objectManager->remove($object);
        }
        $objectManager->flush();
    }

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
    static protected function createKernel(array $options = array())
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
