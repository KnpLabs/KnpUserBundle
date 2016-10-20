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

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManager as BaseUserManager;
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\PasswordUpdaterInterface;

class UserManager extends BaseUserManager
{
    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param PasswordUpdaterInterface $passwordUpdater
     * @param CanonicalFieldsUpdater   $canonicalFieldsUpdater
     * @param string                   $class
     */
    public function __construct(PasswordUpdaterInterface $passwordUpdater, CanonicalFieldsUpdater $canonicalFieldsUpdater, $class)
    {
        parent::__construct($passwordUpdater, $canonicalFieldsUpdater);

        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteUser(UserInterface $user)
    {
        if (!$user instanceof \Persistent) {
            throw new \InvalidArgumentException('This user instance is not supported by the Propel UserManager implementation');
        }

        $user->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserBy(array $criteria)
    {
        $query = $this->createQuery();

        foreach ($criteria as $field => $value) {
            $method = 'filterBy'.ucfirst($field);
            $query->$method($value);
        }

        return $query->findOne();
    }

    /**
     * {@inheritdoc}
     */
    public function findUsers()
    {
        return $this->createQuery()->find();
    }

    /**
     * {@inheritdoc}
     */
    public function reloadUser(UserInterface $user)
    {
        if (!$user instanceof \Persistent) {
            throw new \InvalidArgumentException('This user instance is not supported by the Propel UserManager implementation');
        }

        $user->reload();
    }

    /**
     * {@inheritdoc}
     */
    public function updateUser(UserInterface $user)
    {
        if (!$user instanceof \Persistent) {
            throw new \InvalidArgumentException('This user instance is not supported by the Propel UserManager implementation');
        }

        $this->updateCanonicalFields($user);
        $this->updatePassword($user);
        $user->save();
    }

    /**
     * Create the propel query class corresponding to your queryclass.
     *
     * @return \ModelCriteria the queryClass
     */
    protected function createQuery()
    {
        return \PropelQuery::from($this->class);
    }
}
