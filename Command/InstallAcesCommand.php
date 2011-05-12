<?php

namespace FOS\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

/*
 * This file is part of the FOS\UserBundle
 *
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * This command installs global access control entries (ACEs)
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class InstallAcesCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:installAces')
            ->setDescription('Installs global ACEs')
            ->setHelp(<<<EOT
This command should be run once during the installation process of the entire bundle.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $acesInstaller = $this->container->get('fos_user.aces_installer');

        if (!$acesInstaller->hasAclProvider()) {
            $output->writeln('You must setup the ACL system, see the Symfony2 documentation for how to do this.');
            return;
        }

        try {
            $acesInstaller->install();
        } catch (AclAlreadyExistsException $exists) {
            $output->writeln('You already installed the global ACEs.');
            return;
        }

        $output->writeln('Global ACEs have been installed.');
    }
}
