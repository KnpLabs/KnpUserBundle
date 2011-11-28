<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Propel;

use FOS\UserBundle\Model\Group as ModelGroup;

class GroupProxy extends ModelGroup
{
    protected $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
        $this->updateFormPropel();
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function __call($method, $arguments)
    {
        if (is_callable(array($this->getGroup(), $method))) {
            return call_user_func_array(array($this->getGroup(), $method), $arguments);
        }

        throw new \BadMethodCallException('Can\'t call method '.$method);
    }

    public function save()
    {
        $this->updatePropelGroup();
        $this->getGroup()->save();

        return $this;
    }

    protected function updateFormPropel()
    {
        $group = $this->getGroup();

        $this->name = $group->getName();
        $this->roles = $group->getRoles();
    }

    protected function updatePropelGroup()
    {
        $group = $this->getGroup();

        $group->setName($this->name);
        $group->setRoles($this->roles);
    }
}
