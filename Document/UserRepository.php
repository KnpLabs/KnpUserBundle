<?php

/**
 * This file is part of the Symfony framework.
 *
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Document;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Bundle\DoctrineUserBundle\DAO\UserRepositoryInterface;

class UserRepository extends DocumentRepository implements UserRepositoryInterface
{
    /**
     * @see UserRepositoryInterface::findOneById
     */
    public function findOneById($id)
    {
        return $this->find($id);
    }

    /**
     * @see UserRepositoryInterface::findOneByUsername
     */
    public function findOneByUsername($username)
    {
        return $this->findOneBy(array('username' => $username));
    }

}
