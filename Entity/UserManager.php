<?php

namespace Bundle\FOS\UserBundle\Entity;

use Bundle\FOS\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraint;
use Bundle\FOS\UserBundle\Model\UserManager as BaseUserManager;

class UserManager extends BaseUserManager
{
    protected $em;
    protected $class;
    protected $repository;

    public function __construct($encoder, $algorithm, EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;

        parent::__construct($encoder, $algorithm);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findUsers()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function updateUser(UserInterface $user)
    {
        $this->updatePassword($user);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function validateUnique($value, Constraint $constraint)
    {
        $fields = array_map('trim', explode(',', $constraint->property));
        $users = $this->findConflictualUsers($value, $fields);

        // there is no conflictual user
        if (empty($users)) {
            return true;
        }

        // there is no conflictual user which is not the same as the value
        if (!is_string($value) && $this->anyIsUser($value, $users)) {
            return true;
        }

        return false;
    }

    /**
     * Indicates whether the given user and all compared objects correspond to the same record
     *
     * @param UserInterface $user
     * @param array $comparisons
     * @return boolean
     */
    protected function anyIsUser($user, array $comparisons)
    {
        foreach ($comparisons as $comparison) {
            if (!$user->isUser($comparison)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets conflictual users for the given user and constraint
     *
     * @param UserInterface|string $value
     * @param array $fields
     * @return array
     */
    protected function findConflictualUsers($value, array $fields)
    {
        return $this->repository->findBy($this->getCriteria($value, $fields));
    }

    /**
     * Gets the criteria used to find conflictual entities
     *
     * @param UserInterface|string $value
     * @param array $constraint
     * @return array
     */
    protected function getCriteria($value, array $fields)
    {
        $metadata = $this->em->getClassMetadata($this->class);

        $criteria = array();
        foreach ($fields as $field) {
            if (!$metadata->hasField($field)) {
                throw new \InvalidArgumentException(sprintf('The "%s" class metadata does not have any "%s" field or association mapping.', $this->class, $field));
            }

            $criteria[$field] = is_string($value) ? $value : $metadata->getFieldValue($value, $field);
        }

        return $criteria;
    }
}
