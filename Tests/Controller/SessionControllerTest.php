<?php

namespace Bundle\ArticleBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// Kernel creation required namespaces
use Symfony\Components\Finder\Finder;

class SessionControllerTest extends WebTestCase
{
    public function testNew()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/session/new');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals(1, $crawler->filter('form.doctrine_user_session_new')->count());
    }

    public static function setUpBeforeClass()
    {
        $userRepo = self::staticCreateKernel()->getContainer()->getDoctrineUser_UserRepoService();
        $objectManager = $userRepo->getObjectManager();

        $userClass = $userRepo->getObjectClass();
        $user = new $userClass();
        $user->setUserName('harry_test');
        $user->setEmail('harry@mail.org');
        $user->setPassword('changeme');
        $objectManager->persist($user);

        $user2 = new $userClass();
        $user2->setUserName('harry_test2');
        $user2->setEmail('harry2@mail.org');
        $user2->setPassword('changeme2');
        $objectManager->persist($user2);

        $objectManager->flush();
    }

    static public function tearDownAfterClass()
    {
        $userRepo = self::staticCreateKernel()->getContainer()->getDoctrineUser_UserRepoService();
        $objectManager = $userRepo->getObjectManager();
        foreach(array('harry_test', 'harry_test2') as $username) {
            $objectManager->remove($userRepo->findOneByUsername($username));
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
    static protected function staticCreateKernel(array $options = array())
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
