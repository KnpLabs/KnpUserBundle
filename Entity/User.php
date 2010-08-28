<?php

/**
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Bundle\DoctrineUserBundle\DAO\User as AbstractUser;

/**
 * @Entity(repositoryClass="Bundle\DoctrineUserBundle\Entity\UserRepository")
 * @Table(name="doctrine_user_user")
 * @HasLifecycleCallbacks
 */
class User extends AbstractUser
{
    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="username", type="string", length=255, unique=true)
     */
    protected $username;

    /**
     * @Column(name="email", type="string", length=255, unique=true, nullable=false)
     */
    protected $email;

    /**
     * @Column(name="is_active", type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @Column(name="is_super_admin", type="boolean", nullable=false)
     */
    protected $isSuperAdmin;

    /**
     * @Column(name="password", type="string", length=127, nullable=false)
     */
    protected $password;

    /**
     * @Column(name="algorithm", type="string", length=127)
     */
    protected $algorithm;

    /**
     * @Column(name="salt", type="string", length=127, nullable=false)
     */
    protected $salt;

    /**
     * @Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     * @Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @ManyToMany(targetEntity="Group")
     * @JoinTable(name="doctrine_user_users_group",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @ManyToMany(targetEntity="Permission")
     * @JoinTable(name="doctrine_user_users_permission",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="permission_id", referencedColumnName="id")}
     * )
     */
    protected $permissions;

    public function __construct()
    {
        parent::__construct();

        $this->groups = new ArrayCollection();
        $this->permissions = new ArrayCollection();
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

    /**
     * @see Bundle\DoctrineUserBundle\DAO\User::getGroupNames
     */
    public function getGroupNames()
    {
        return $this->groups->map(function($groups) {
            $names = array();

            foreach($groups as $group) {
                $names[] = $group->getName();
            }

            return $names;
        });
    }

    /**
     * @see Bundle\DoctrineUserBundle\DAO\User::getPermissionNames
     */
    public function getPermissionNames()
    {
        return $this->permissions->map(function($permissions) {
            $names = array();

            foreach ($permissions as $permission) {
                $names[] = $permission->getName();
            }

            return $names;
        });
    }

    /**
     * @see Bundle\DoctrineUserBundle\DAO\User::getAllPermissionNames
     */
    public function getAllPermissionNames()
    {
        $names = $this->getPermissionNames();

        foreach ($this->groups as $group) {
            $names = array_merge($names, $group->getPermissionNames());
        }

        return $names;
    }
}
