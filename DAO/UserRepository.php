<?php

/**
 * (c) Gordon Franke <info@nevalon.de>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\DAO;

class UserRepository
{
    /**
     * An EntityManager or DocumentManager 
     * @var mixed
     */
    protected $objectManager;

    /**
     * The object class, like Bundle\DoctrineUserBundle\Entity\User or Bundle\DoctrineUserBundle\Document\User 
     * @var string
     */
    protected $objectClass;

    /**
     * Create a DAO\UserRepository 
     * @param Object $objectManager An EntityManager or a DocumentManager
     * @param string $objectClass The object class, like Bundle\DoctrineUserBundle\Entity\User or Bundle\DoctrineUserBundle\Document\User 
     */
    public function __construct($objectManager, $objectClass)
    {
        $this->objectManager = $objectManager;
        $this->objectClass = $objectClass;
    }

    /**
     * Find a user by its id
     * @param   mixed  $id
     * @return  User or null if user does not exist
     */
    public function findOneById($id)
    {
        return $this->getDriver()->findOneById($id);
    }

    /**
     * Find a user by its identifier
     * @param   mixed  $identifier
     * @return  User or null if user does not exist
     */
    public function findOneByIdentifier($identifier)
    {
        return $this->findOneById($identifier);
    }

    /**
     * Find a user by its username
     * @param   string  $username
     * @return  User or null if user does not exist
     */
    public function findOneByUsername($username)
    {
        return $this->getDriver()->findOneByUsername($username);
    }

    /**
     * Find a user by its email
     * @param   string  $email
     * @return  User or null if user does not exist
     */
    public function findOneByEmail($email)
    {
        return $this->getDriver()->findOneByEmail($email);
    }

    /**
     * Find a user by its username or email
     * @param   string  $usernameOrEmail
     * @return  User or null if user does not exist
     */
    public function findOneByUsernameOrEmail($usernameOrEmail)
    {
        return $this->getDriver()->findOneByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * Find a user by its username, and check the password
     * @param   string  $username
     * @param   string  $password
     * @return  User or null if user does not exist
     */
    public function findOneByUsernameAndPassword($username, $password)
    {
        $user = $this->findOneByUsername($username);

        if($user && $user->checkPassword($password))
        {
            return $user;
        }
    }

    /**
     * Find a user by its email, and check the password
     * @param   string  $email
     * @param   string  $password
     * @return  User or null if user does not exist
     */
    public function findOneByEmailAndPassword($email, $password)
    {
        $user = $this->findOneByEmail($email);

        if($user && $user->checkPassword($password))
        {
            return $user;
        }
    }

    /**
     * Find a user by its username or email, and check the password
     * @param   string  $username
     * @param   string  $password
     * @return  User or null if user does not exist
     */
    public function findOneByUsernameOrEmailAndPassword($usernameOrEmail, $password)
    {
        $user = $this->findOneByUsernameOrEmail($usernameOrEmail);

        if($user && $user->checkPassword($password))
        {
            return $user;
        }
    }
    
    /**
     * The Repository Driver
     * @return mixed
     **/
    public function getDriver()
    {
        return $this->objectManager->getRepository($this->objectClass);
    }
    
    /**
     * Get objectManager
     * @return mixed
     */
    public function getObjectManager()
    {
      return $this->objectManager;
    }
    
    /**
     * Set objectManager
     * @param  mixed
     * @return null
     */
    public function setObjectManager($objectManager)
    {
      $this->objectManager = $objectManager;
    }
    
    /**
     * Get objectClass
     * @return string
     */
    public function getObjectClass()
    {
      return $this->objectClass;
    }
    
    /**
     * Set objectClass
     * @param  string
     * @return null
     */
    public function setObjectClass($objectClass)
    {
      $this->objectClass = $objectClass;
    }
}
