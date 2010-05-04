<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Entities;

/**
 * @Entity
 * @Table(name="sf_doctrine_user")
 */
class DoctrineUser
{
  /**
   * @Column(name="id", type="integer")
   * @Id
   * @GeneratedValue(strategy="AUTO")
   */
  protected $id;
  
  /**
   * @Column(name="created_at", type="datetime")
   */
  protected $createdAt;

  /**
   * @Column(name="created_at", type="datetime")
   */
  protected $updatedAt;

  /**
   * @Column(name="last_login", type="datetime")
   */
  protected $lastLogin;

  /**
   * @Column(name="username", type="string", length=255, unique=true)
   */
  protected $username;

  /**
   * @Column(name="algorithm", type="string", length=128, nullable=true)
   */
  protected $algorithm;

  /**
   * @Column(name="salt", type="string", length=128)
   */
  protected $salt;
  
  /**
   * @Column(name="password", type="string", length=128)
   */
  protected $password;

  /**
   * @Column(name="is_active", type="boolean", nullable=false)
   */
  protected $isActive;

  /**
   * @Column(name="is_super_admin", type="boolean", nullable=false)
   */
  protected $isSuperActive;


  public function __construct()
  {
    $this->createdAt = new \DateTime();
    $this->algorithm = 'sha1';
    $this->isActive = true;
    $this->isSuperAdmin = false;
  }

  public function getCreatedAt()
  {
    return $this->createdAt;
  }
}