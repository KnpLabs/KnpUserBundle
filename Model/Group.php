<?php

namespace Bundle\DoctrineUserBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Storage agnostic group object
 * Has validator annotation, but database mapping must be done in a subclass.
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
abstract class Group
{
    protected $id;

    /**
     * @validation:Validation({
     *      @validation:NotBlank(),
     *      @validation:MaxLength(limit=255)
     * })
     */
    protected $name;

    /**
     * @validation:MaxLength(limit=5000)
     */
    protected $description;

    /**
     * @validation:DateTime()
     */
    protected $createdAt;

    /**
     * @validation:DateTime()
     */
    protected $updatedAt;

    /**
     * @var Collection
     */
    protected $permissions;

    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * This method must be called just before inserting the object into the
     * database. Don't call it otherwise!
     */
    public function incrementCreatedAt()
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }

    /**
     * This method must be called just before updating the object into the
     * database. Don't call it otherwise!
     */
    public function incrementUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get permissions granted to the group 
     * 
     * @return Collection
     */
    public function getPermissions()
    {
        return $this->permissions ?: $this->permissions = new ArrayCollection();
    }

    /**
     * Gets the name of the permissions granted to the group
     *
     * @return array
     */
    public function getPermissionNames()
    {
        $names = array();
        foreach ($this->getPermissions() as $permission) {
            $names[] = $permission->getName();
        }

        return $names;
    }

    /**
     * Add a permission to the group permissions
     *
     * @param Permission $permission
     * @return null
     **/
    public function addPermission(Permission $permission)
    {
        if (!$this->getPermissions()->contains($permission)) {
            $this->getPermissions()->add($permission);
        }
    }

    public function __toString()
    {
        return $this->getName();
    }
}
