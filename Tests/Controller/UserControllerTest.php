<?php

namespace Bundle\DoctrineUserBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// Kernel creation required namespaces
use Symfony\Component\Finder\Finder;

class UserControllerTest extends WebTestCase
{
    public function testNew()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'doctrine_user_user_new'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('form.doctrine_user_user_new')->count());
    }

    public function testCreateSuccess()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'doctrine_user_user_new'));
        $form = $crawler->selectButton('Create user')->form();
        $client->submit($form, array('doctrine_user_user_new[username]' => 'harry_test', 'doctrine_user_user_new[email]' => 'harry_test@email.org', 'doctrine_user_user_new[password]' => 'changeme'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('div.doctrine_user_user_create_success')->count());
        $this->assertRegexp('/harry_test/', $client->getResponse()->getContent());
    }

    public function testCreateEmptyFormError()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'doctrine_user_user_new'));
        $form = $crawler->selectButton('Create user')->form();
        $client->submit($form, array());
        $this->assertFalse($client->getResponse()->isRedirect());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->filter('form.doctrine_user_user_new');
        $this->assertEquals(1, $form->count());
        $this->assertRegexp('/This value should not be blank/', $client->getResponse()->getContent());
        $this->assertEquals(0, $crawler->filter('div.doctrine_user_user_create_success')->count());
    }

    /**
     * @depends testCreateSuccess
     */
    public function testShow()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'doctrine_user_user_show', array('username' => 'harry_test')));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertRegexp('/harry_test/', $client->getResponse()->getContent());
    }

    /**
     * @depends testCreateSuccess
     */
    public function testList()
    {
        $userRepo = self::staticCreateKernel()->getContainer()->getDoctrineUser_UserRepositoryService();
        $userClass = $userRepo->getObjectClass();
        $objectManager = $userRepo->getObjectManager();

        $user2 = new $userClass();
        $user2->setUsername('harry_test2');
        $user2->setEmail('harry2@mail.org');
        $user2->setPassword('changeme2');
        $objectManager->persist($user2);

        $objectManager->flush();

        $client = $this->createClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'doctrine_user_user_list'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertRegexp('/harry_test/', $client->getResponse()->getContent());
        $this->assertRegexp('/harry_test2/', $client->getResponse()->getContent());
    }

    /**
     * @depends testCreateSuccess
     */
    public function testEdit()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'doctrine_user_user_edit', array('username' => 'harry_test')));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('form.doctrine_user_user_edit')->count());
    }

    /**
     * @depends testCreateSuccess
     */
    public function testUpdateSuccess()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'doctrine_user_user_edit', array('username' => 'harry_test')));
        $form = $crawler->selectButton('Update user')->form();
        $client->submit($form, array('doctrine_user_user_edit[username]' => 'harry_test_modified', 'doctrine_user_user_edit[email]' => 'harry_test_modified@email.org', 'doctrine_user_user_edit[password]' => 'changeme'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('div.doctrine_user_user_update_success')->count());
        $this->assertRegexp('/harry_test_modified/', $client->getResponse()->getContent());
    }

    /**
     * @depends testUpdateSuccess
     */
    public function testUpdateEmptyFormError()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'doctrine_user_user_edit', array('username' => 'harry_test_modified')));
        $form = $crawler->selectButton('Update user')->form();
        $client->submit($form, array('doctrine_user_user_edit[username]' => '', 'doctrine_user_user_edit[email]' => '', 'doctrine_user_user_edit[password]' => ''));
        $this->assertFalse($client->getResponse()->isRedirect());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->filter('form.doctrine_user_user_edit');
        $this->assertEquals(1, $form->count());
        $this->assertRegexp('/This value should not be blank/', $client->getResponse()->getContent());
        $this->assertEquals(0, $crawler->filter('div.doctrine_user_user_update_success')->count());
    }

    /**
     * @depends testUpdateEmptyFormError
     */
    public function testUpdateEmptyFormErrorThenRetrySuccess()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'doctrine_user_user_edit', array('username' => 'harry_test_modified')));
        $form = $crawler->selectButton('Update user')->form();
        $client->submit($form, array('doctrine_user_user_edit[username]' => '', 'doctrine_user_user_edit[email]' => '', 'doctrine_user_user_edit[password]' => ''));
        $form = $crawler->selectButton('Update user')->form();
        $client->submit($form, array('doctrine_user_user_edit[username]' => 'harry_test', 'doctrine_user_user_edit[email]' => 'harry_test@email.org', 'doctrine_user_user_edit[password]' => 'changeme'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('div.doctrine_user_user_update_success')->count());
        $this->assertRegexp('/harry_test/', $client->getResponse()->getContent());
    }

    public function testDelete()
    {
        $userRepo = self::staticCreateKernel()->getContainer()->getDoctrineUser_UserRepositoryService();
        $userClass = $userRepo->getObjectClass();
        $objectManager = $userRepo->getObjectManager();

        $user = new $userClass();
        $user->setUserName('harry_test3');
        $user->setEmail('harry3@mail.org');
        $user->setPassword('changeme3');
        $objectManager->persist($user);

        $objectManager->flush();

        $client = $this->createClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'doctrine_user_user_delete', array('username' => 'harry_test3')));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertNotRegexp('/harry_test3/', $client->getResponse()->getContent());
    }

    protected function generateUrl($client, $route, array $params = array())
    {
        return $client->getContainer()->get('router')->generate($route, $params);
    }

    static public function tearDownAfterClass()
    {
        $userRepo = self::staticCreateKernel()->getContainer()->getDoctrineUser_UserRepositoryService();
        $objectManager = $userRepo->getObjectManager();
        foreach(array('harry_test', 'harry_test2', 'harry_test_modified') as $username) {
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
