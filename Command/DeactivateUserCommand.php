<?php

namespace Bundle\DoctrineUserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Bundle\DoctrineUserBundle\Entity\User;

/*
 * This file is part of the DoctrineUserBundle
 *
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * DeactivateUserCommand.
 *
 * @package    Bundle
 * @subpackage DoctrineUserBundle
 * @author     Antoine Hérault <antoine.herault@gmail.com>
 */
class DeactivateUserCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:user:deactivate')
            ->setDescription('Deactivate a user')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
            ))
            ->setHelp(<<<EOT
The <info>doctrine:user:deactivate</info> command deactivates a user (will not be able to log in)

  <info>php app/console doctrine:user:deactivate matthieu</info>
EOT
        );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userRepo = $this->container->get('doctrine_user.repository.user');
        $user = $userRepo->findOneByUsername($input->getArgument('username'));

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('The user "%s" does not exist', $input->getArgument('username')));
        }
        $user->setIsActive(false);

        $userRepo->getObjectManager()->persist($user);
        $userRepo->getObjectManager()->flush();

        $output->writeln(sprintf('User "%s" has been deactivated.', $user->getUsername()));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a username:',
                function($username)
                {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }
                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }
    }
}
