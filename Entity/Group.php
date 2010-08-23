<?php

namespace Bundle\DoctrineUserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Bundle\DoctrineUserBundle\DAO\Group as AbstractGroup;

/**
 * @Entity(repositoryClass="Bundle\DoctrineUserBundle\Entity\GroupRepository")
 * @Table(name="doctrine_user_group")
 * @HasLifecycleCallbacks
 */
class Group extends AbstractGroup
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
     * @ManyToMany(targetEntity="Permission")
     * @JoinTable(name="sf_doctrine_user_groups_permission",
     *      joinColumns={@JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="permission_id", referencedColumnName="id")}
     * )
     */
    protected $permissions;

    public function __construct()
    {
        parent::__construct();

        $this->permissions = new ArrayCollection();
    }

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
        return $this->permissions->map(function($permissions) {
           $names = array();
           
           foreach ($permissions as $permission) {
               $names[] = $permission->getName();
           }

           return $names;
        });
    }
}
