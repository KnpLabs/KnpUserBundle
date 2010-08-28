<?php

namespace Bundle\DoctrineUserBundle\DAO;

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
     * @Validation({
     *      @NotBlank(),
     *      @MaxLength(limit=255)
     * })
     */
    protected $name;

    /**
     * @Validation({
     *      
     * })
     */
    protected $description;

    /**
     * @Validation({
     *      @DateTime()
     * })
     */
    protected $createdAt;

    /**
     * @Validation({
     *      @DateTime()
     * })
     */
    protected $updatedAt;

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
        if(null === $this->createdAt)
        {
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
     * Gets the name of the permissions granted to the group
     */
    abstract public function getPermissionNames();

    public function __toString()
    {
        return $this->getName();
    }
}
