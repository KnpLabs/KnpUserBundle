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
        $fields   = $this->extractFieldNames($constraint->property);
        $entities = $this->getConflictualEntities($value, $fields);

        // there is no conflictual entity
        if (0 === count($entities)) {
            return true;
        }

        // there is no conflictual entity which is not the same as the value
        if ($this->areAllTheSame($value, $entities)) {
            return true;
        }

        return false;
    }

    /**
     * Indicates whether the given entity and all compared objects correspond to the same record
     *
     * @param UserInterface $entity
     * @param array $comparisons
     * @return boolean
     */
    protected function areAllTheSame($entity, array $comparisons)
    {
        foreach ($comparisons as $comparison) {
            if ( ! $this->areTheSame($entity, $comparison)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Indicates whether the given entity and compared object correspond to the same record
     *
     * @param UserInterface $entity
     * @param mixed $comparison
     * @return boolean
     */
    protected function areTheSame($entity, $comparison)
    {
        return $entity === $comparison;
    }

    /**
     * Gets conflictual entities for the given entity and constraint
     *
     * @param UserInterface $entity
     * @param array $fields
     * @return array
     */
    protected function getConflictualEntities($entity, array $fields)
    {
        return $this->repository->findBy($this->getCriteria($entity, $fields));
    }

    /**
     * Gets the criteria used to find conflictual entities
     *
     * @param UserInterface $entity
     * @param array $constraint
     * @return array
     */
    protected function getCriteria($entity, array $fields)
    {
        $criteria = array();
        $metadata = $this->em->getClassMetadata(get_class($entity));
        foreach ($fields as $field) {
            if ($metadata->hasField($field)) {
                $criteria[$field] = $metadata->getFieldValue($entity, $field);
            } else {
                throw new \InvalidArgumentException(sprintf('The "%s" class metadata does not have any "%s" field or association mapping.', get_class($entity), $field));
            }
        }

        return $criteria;
    }

    /**
     * Extracts field names from the given constraint
     *
     * @param  string $fields
     * @return array
     */
    protected function extractFieldNames($fields)
    {
        return array_map('trim', explode(',', $fields));
    }
}
