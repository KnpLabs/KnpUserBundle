<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\DAO;

/**
 * Storage agnostic permission object
 * Has validator annotation, but database mapping must be done in a subclass.
 */
abstract class Permission
{
    protected $id;

    /**
     * @Validation({
     *      @NotBlank(),
     *      @MinLength(limit=2),
     *      @MaxLength(limit=255)
     * })
     */
    protected $name;

    /**
     * @Validation({
     *      @NotBlank(),
     *      @MinLength(limit=2),
     *      @MaxLength(limit=255)
     * })
     */
    protected $description;

    protected $createdAt;

    protected $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
      return $this->description;
    }
    
    /**
     * Set description
     * @param  string
     * @return null
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

    public function __toString()
    {
        return $this->getName();
    }

    /** @PrePersist */
    public function incrementCreatedAt()
    {
        if(null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }

    /** @PreUpdate */
    public function incrementUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }
}
