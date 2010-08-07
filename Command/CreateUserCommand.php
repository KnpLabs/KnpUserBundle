<?php

namespace Bundle\DoctrineUserBundle\Command;

use Symfony\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Components\Console\Input\InputArgument;
use Symfony\Components\Console\Input\InputOption;
use Symfony\Components\Console\Input\InputInterface;
use Symfony\Components\Console\Output\OutputInterface;
use Symfony\Components\Console\Output\Output;
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
 * CreateUserCommand.
 *
 * @package    Bundle
 * @subpackage DoctrineUserBundle
 * @author     Matthieu Bontemps <matthieu@knplabs.com>
 * @author     Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class CreateUserCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:user:create')
            ->setDescription(
                'Create a user.'
            )
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('super-admin', null, InputOption::PARAMETER_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::PARAMETER_NONE, 'Set the user as inactive'),
            ))
            ->addOption('em', null, InputOption::PARAMETER_OPTIONAL, 'The entity manager to use for this command.')
            ->setHelp(<<<EOT
The <info>doctrine:user:create</info> command creates a user:

  <info>./symfony doctrine:user:create matthieu</info>

This interactive shell will first ask you for a password.

You can alternatively specify the password as a second argument:

  <info>./symfony doctrine:user:create matthieu mypassword</info>

You can create a super admin via the super-admin flag:

  <info>./symfony doctrine:user:create admin --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>./symfony doctrine:user:create thibault --inactive</info>
  
EOT
        );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        DoctrineCommand::setApplicationEntityManager($this->application, $input->getOption('em'));

        $user = $this->getHelper('em')->getEntityManager()
        ->getRepository($this->container->getParameter('doctrine_user.user_object.class'))
        ->createUser(
            $input->getArgument('username'),
            $input->getArgument('password'),
            !$input->getOption('inactive'),
            $input->getOption('super-admin')
        );
        
        $output->writeln(sprintf('Created user <comment>%s</comment>', $user->getUsername()));
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if(null === $input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a password:',
                function($password) {
                    if(empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }
                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }
    }
}
