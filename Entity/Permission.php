<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Entity;
use Bundle\DoctrineUserBundle\DAO\Permission as AbstractPermission;

/**
 * @Entity(repositoryClass="Bundle\DoctrineUserBundle\Entity\PermissionRepository")
 * @Table(name="sf_doctrine_permission")
 * @HasLifecycleCallbacks
 */
class Permission extends AbstractPermission
{
    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="name", type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @Column(name="description", type="text", length=5000, nullable=true)
     */
    protected $description;
    
    /**
     * @Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;
}
