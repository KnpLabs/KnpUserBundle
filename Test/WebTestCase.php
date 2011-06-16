<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Test;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    protected function runCommand($name, array $params = array())
    {
        \array_unshift($params, $name);

        $kernel = self::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput($params);
        $input->setInteractive(false);

        $ouput = new NullOutput(0);

        $application->run($input, $ouput);
    }

    protected function removeTestUser()
    {
        $userManager = self::createKernel()->getContainer()->getService('fos_user.user_manager');
        if ($user = $userManager->findUserByUsername('test_username')) {
            $userManager->deleteUser($user);
        }
    }
}
