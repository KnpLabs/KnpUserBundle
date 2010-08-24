<?php

namespace Bundle\DoctrineUserBundle\Document;
use Bundle\DoctrineUserBundle\DAO\Permission as AbstractPermission;

/**
 * @Document(
 *   collection="doctrine_user_permission",
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


    /**
     * @PrePersist
     */
    public function incrementCreatedAt()
    {
        parent::incrementCreatedAt();
    }

    /**
     * @PreUpdate
     */
    public function incrementUpdatedAt()
    {
        parent::incrementUpdatedAt();
    }
}
