<?php

namespace Bundle\DoctrineUserBundle\Document;
use Bundle\DoctrineUserBundle\DAO\Group as AbstractGroup;

/**
 * @Document(
 *   collection="doctrine_user_group",
 *   indexes={
 *     @Index(keys={"name"="asc"})
 *   },
 *   repositoryClass="Bundle\DoctrineUserBundle\Document\GroupRepository"
 * )
 * @HasLifecycleCallbacks
 */
class Group extends AbstractGroup
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
     * PreUpdate
     */
    public function incrementUpdatedAt()
    {
        parent::incrementUpdatedAt();
    }

    /**
     * @see Bundle\DoctrineUserBundle\DAO\Group::getPermissionNames
     */
    public function getPermissionNames()
    {
        throw new \Exception('Not implemented yet.');
    }
}
