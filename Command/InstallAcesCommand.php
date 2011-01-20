<?php

namespace FOS\UserBundle\Command;

use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use FOS\UserBundle\Model\User;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

/*
 * This file is part of the FOS\UserBundle
 *
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * This command installs global access control entries (ACEs)
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class InstallAcesCommand extends BaseCommand
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
        if (!$this->container->has('security.acl.provider')) {
            $output->writeln('You must setup the ACL system, see the Symfony2 documentation for how to do this.');
            return;
        }

        $provider = $this->container->get('security.acl.provider');
        $oid = new ObjectIdentity('class', $this->container->get('fos_user.user_manager')->getClass());
        try {
            $acl = $provider->createAcl($oid);
        } catch (AclAlreadyExistsException $exists) {
            $output->writeln('You already installed the global ACEs.');
            return;
        }

        // insert ACEs for the super admin
        $sid = new RoleSecurityIdentity(User::ROLE_SUPERADMIN);
        $acl->insertClassAce($sid, MaskBuilder::MASK_IDDQD);

        $provider->updateAcl($acl);

        $output->writeln('Global ACEs have been installed.');
    }
}
