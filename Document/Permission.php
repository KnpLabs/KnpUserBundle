<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Document;
use Bundle\DoctrineUserBundle\DAO\Permission as AbstractPermission;

/**
 * @Document(
 *   collection="sf_doctrine_permission",
 *   indexes={
 *     @Index(keys={"name"="asc"})
 *   },
 *   repositoryClass="Bundle\DoctrineUserBundle\Document\PermissionRepository"
 * )
 * @HasLifecycleCallbacks
 */
class Permission extends AbstractPermission
{
    /**
     * @Id
     */
    protected $id;

    /**
     * @Field(type="string")
     */
    protected $name;

    /**
     * @Field(type="string")
     */
    protected $description;
    
    /**
     * @Field(type="date")
     */
    protected $createdAt;

    /**
     * @Field(type="date")
     */
    protected $updatedAt;
}
