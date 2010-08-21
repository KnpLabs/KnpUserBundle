<?php

/**
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Document;
use Bundle\DoctrineUserBundle\DAO\User as AbstractUser;

/**
 * @Document(
 *   collection="sf_doctrine_user",
 *   indexes={
 *     @Index(keys={"username"="asc"})
 *   },
 *   repositoryClass="Bundle\DoctrineUserBundle\Document\UserRepository"
 * )
 * @HasLifecycleCallbacks
 */
class User extends AbstractUser
{
    /**
     * @Id
     */
    protected $id;
    /**
     * @Field(type="string")
     */
    protected $username;
    /**
     * @Field(type="string")
     */
    protected $email;
    /**
     * @Field(type="boolean")
     */
    protected $isActive;
    /**
     * @Field(type="boolean")
     */
    protected $isSuperAdmin;
    /**
     * @Field(type="string")
     */
    protected $password;
    /**
     * @Field(type="string")
     */
    protected $algorithm;
    /**
     * @Field(type="string")
     */
    protected $salt;
    /**
     * @Field(type="date")
     */
    protected $createdAt;
    /**
     * @Field(type="date")
     */
    protected $updatedAt;
    /**
     * @Field(type="date")
     */
    protected $lastLogin;

    public function __construct()
    {
        parent::__construct();

        $this->groups = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    /**
     * @see Bundle\DoctrineUserBundle\DAO\User::incrementCreatedAt
     * @PreCreate
     */
    public function incrementCreatedAt()
    {
        parent::incrementCreatedAt();
    }

    /**
     * @see Bundle\DoctrineUserBundle\DAO\User::incrementUpdatedAt
     * @PreUpdate
     */
    public function incrementUpdatedAt()
    {
        parent::incrementUpdatedAt();
    }

    /**
     * @see Bundle\DoctrineUserBundle\DAO\User::getGroupNames
     */
    public function getGroupNames()
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * @see Bundle\DoctrineUserBundle\DAO\User::getPermissionNames
     */
    public function getPermissionNames()
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * @see Bundle\DoctrineUserBundle\DAO\User::getAllPermissionNames
     */
    public function getAllPermissionNames()
    {
        throw new \Exception('Not implemented yet.');
    }
}
