<?php

/**
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Entity;
use Bundle\DoctrineUserBundle\DAO\User as AbstractUser;

/**
 * @Entity(repositoryClass="Bundle\DoctrineUserBundle\Entity\UserRepository")
 * @Table(name="sf_doctrine_user")
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
}
