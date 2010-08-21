<?php

namespace Bundle\DoctrineUserBundle\Entity;
use Bundle\DoctrineUserBundle\DAO\Permission as AbstractPermission;

/**
 * @Entity(repositoryClass="Bundle\DoctrineUserBundle\Entity\PermissionRepository")
 * @Table(name="sf_doctrine_user_permission")
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
     * @Column(name="name", type="string", unique=true)
     */
    protected $name;
    /**
     * @Column(name="description", type="text", nullable=true)
     */
    protected $description;
    /**
     * @Column(name="created_at", type="datetime")
     */
    protected $createdAt;
    /**
     * @Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @PreInsert
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
}
