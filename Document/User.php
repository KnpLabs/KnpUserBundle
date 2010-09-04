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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Document(
 *   collection="doctrine_user_user",
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

    /**
     * @Field(type="string")
     */
    protected $confirmationToken;

    /**
     * @ReferenceMany(targetDocument="Permission")
     */
    protected $permissions;

    /**
     * @ReferenceMany(targetDocument="Group")
     */
    protected $groups;

    public function __construct()
    {
        parent::__construct();

        $this->permissions = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    /**
     * @see Bundle\DoctrineUserBundle\DAO\User::incrementCreatedAt
     * @PrePersist
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
}
