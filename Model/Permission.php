<?php

namespace Bundle\DoctrineUserBundle\Model;

/**
 * Storage agnostic permission object
 * Has validator annotation, but database mapping must be done in a subclass.
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
abstract class Permission
{
    protected $id;

    /**
     * @validation:Validation({
     *      @validation:NotBlank(),
     *      @validation:MaxLength(limit=255)
     * })
     */
    protected $name;

    protected $description;
    
    /**
     * @validation:DateTime()
     */
    protected $createdAt;

    /**
     * @validation:DateTime()
     */
    protected $updatedAt;

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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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

    public function __toString()
    {
        return $this->getName();
    }
}
