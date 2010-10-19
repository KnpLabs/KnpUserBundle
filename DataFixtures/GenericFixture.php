<?php

namespace Bundle\DoctrineUserBundle\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface,
    Symfony\Component\HttpKernel\Kernel;

abstract class GenericFixture implements FixtureInterface
{

    public function load($manager)
    {
        $container = $this->getContainer();


        //Permissions
        $permissionClass = $container->get('doctrine_user.permission_repository')->getObjectClass();

        $nbPermissions = 5;
        for ($it = 1; $it <= $nbPermissions; $it++) {
            ${'permission'.$it} = new $permissionClass();
            ${'permission'.$it}->setName('permission'.$it);
            ${'permission'.$it}->setDescription('Permission number #'.$it);
            $manager->persist(${'permission'.$it});
        }

        // Groups
        $groupClass = $container->get('doctrine_user.group_repository')->getObjectClass();

        $nbGroups = 5;
        for ($it = 1; $it <= $nbGroups; $it++) {
            ${'group'.$it} = new $groupClass();
            ${'group'.$it}->setName('group'.$it);
            ${'group'.$it}->setDescription('Group number #'.$it);
            $manager->persist(${'group'.$it});
        }

        // Users
        $userClass = $container->get('doctrine_user.user_repository')->getObjectClass();

        $admin = new $userClass();
        $admin->setUsername('admin');
        $admin->setEmail('admin@site.org');
        $admin->setPassword('admin');
        $admin->setIsSuperAdmin(true);
        $manager->persist($admin);


        $nbUsers = 5;
        for ($it = 1; $it <= $nbUsers; $it++) {
            ${'user'.$it} = new $userClass();
            ${'user'.$it}->setUsername('user'.$it);
            ${'user'.$it}->setEmail('user'.$it.'@site.org');
            ${'user'.$it}->setPassword('password'.$it);
            $manager->persist($admin);
        }

        // User permissions
        $user1->addPermission($permission1);
        $user1->addPermission($permission2);
        $manager->persist($user1);

        // Group permissions
        for ($it = 1; $it <= $nbPermissions; $it++) {
            for ($jt = 0; $jt <= 1; $jt++) {
                ${'group'.$it}->addPermission(${'permission'.(($it + $jt) % $nbPermissions + 1)});
            }
            $manager->persist(${'group'.$it});
        }

        // User groups
        for ($it = 1; $it <= $nbUsers; $it++) {
            for ($jt = 0; $jt <= 1; $jt++) {
                ${'user'.$it}->addGroup(${'group'.(($it + $jt) % $nbGroups + 1)});
            }
            $manager->persist(${'user'.$it});
        }
    }

    protected function getContainer()
    {
        foreach ($GLOBALS as $name => $value) {
            if (is_object($value) && ($value instanceof Kernel))
                return $container = $value->getContainer();
        }
    }
}