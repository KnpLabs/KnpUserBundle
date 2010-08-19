<?php

namespace Bundle\DoctrineUserBundle\Tests\Controller;
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

    public function testCreateWithUsernameSuccess()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/session/new');
        $form = $crawler->selectButton('Log in')->form();
        $client->submit($form, array('doctrine_user_session_new[usernameOrEmail]' => 'harry_test', 'doctrine_user_session_new[password]' => 'changeme'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('div.doctrine_user_session_confirmation')->count());
        $this->assertRegexp('/harry_test/', $client->getResponse()->getContent());

        return array($client, $crawler);
    }

    public function testCreateWithEmailSuccess()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/session/new');
        $form = $crawler->selectButton('Log in')->form();
        $client->submit($form, array('doctrine_user_session_new[usernameOrEmail]' => 'harry@mail.org', 'doctrine_user_session_new[password]' => 'changeme'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('div.doctrine_user_session_confirmation')->count());
        $this->assertRegexp('/harry_test/', $client->getResponse()->getContent());
    }

    public function testCreateEmptyFormError()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/session/new');
        $form = $crawler->selectButton('Log in')->form();
        $crawler = $client->submit($form, array());
        $this->assertFalse($client->getResponse()->isRedirect());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('div.doctrine_user_session_new_error')->count());
        $this->assertEquals(1, $crawler->filter('form.doctrine_user_session_new')->count());
    }

    public function testCreateWithBadUsernameError()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/session/new');
        $form = $crawler->selectButton('Log in')->form();
        $crawler = $client->submit($form, array('doctrine_user_session_new[usernameOrEmail]' => 'thisusernamedoesnotexist-atleastihope', 'doctrine_user_session_new[password]' => 'changeme'));
        $this->assertFalse($client->getResponse()->isRedirect());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('div.doctrine_user_session_new_error')->count());
        $this->assertEquals(1, $crawler->filter('form.doctrine_user_session_new')->count());
    }

    /**
     * @depends testCreateWithUsernameSuccess
     */
    public function testLogout(array $dependencies)
    {
        list($client, $crawler) = $dependencies;
        $crawler = $client->click($crawler->selectLink('Log out')->link());
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('form.doctrine_user_session_new')->count());
        $this->assertNotRegexp('/harry_test/', $client->getResponse()->getContent());
    }

    public static function setUpBeforeClass()
    {
        $userRepo = self::staticCreateKernel()->getContainer()->getDoctrineUser_UserRepositoryService();
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
        $userRepo = self::staticCreateKernel()->getContainer()->getDoctrineUser_UserRepositoryService();
        $objectManager = $userRepo->getObjectManager();
        foreach(array('harry_test', 'harry_test2') as $username) {
            if($object = $userRepo->findOneByUsername($username)) {
                $objectManager->remove($object);
            }
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
