<?php

namespace Bundle\FOS\UserBundle\Validator\Doctrine\ORM;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * UniqueValidator
 */
class UniqueValidator extends ConstraintValidator
{
    /**
     * @var  EntityManager
     */
    protected $entityManager;

    /**
     * Contructor
     *
     * @param  EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    /**
     * Sets the entity manager
     *
     * @param  EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    /**
     * Gets the entity manager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Indicates whether the constraint is valid
     *
     * @param  Entity     $value
     * @param  Constraint $constraint
     */
    public function isValid($value, Constraint $constraint)
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

        if (1 === count($fields)) {
            $this->context->setPropertyPath($fields[0]);
        }

        $this->setMessage($constraint->message, array(
            'property' => implode(', ', $fields)
        ));

        return false;
    }

    /**
     * Gets conflictual entities for the given entity and constraint
     *
     * @param  Entity $entity
     * @param  array  $fields
     *
     * @return array
     */
    public function getConflictualEntities($entity, array $fields)
    {
        $repository = $this->entityManager->getRepository(get_class($entity));

        return $repository->findBy($this->getCriteria($entity, $fields));
    }

    /**
     * Indicates whether the given entity and all compared objects correspond to the same record
     *
     * @param  Entity $entity
     * @param  array  $comparisons
     *
     * @return boolean
     */
    public function areAllTheSame($entity, array $comparisons)
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
     * @param  Entity $entity
     * @param  mixed  $comparison
     *
     * @return boolean
     */
    public function areTheSame($entity, $comparison)
    {
        return $entity === $comparison;
    }

    /**
     * Gets the criteria used to find conflictual entities
     *
     * @param  Entity $entity
     * @param  array  $constraint
     *
     * @return array
     */
    public function getCriteria($entity, array $fields)
    {
        $criteria = array();
        $metadata = $this->entityManager->getClassMetadata(get_class($entity));
        foreach ($fields as $field) {
            if ($metadata->hasAssociation($field)) {
                $this->addAssociationCriteria($entity, $field, $metadata, $criteria);
            } elseif ($metadata->hasField($field)) {
                $this->addFieldCriteria($entity, $field, $metadata, $criteria);
            } else {
                throw new \InvalidArgumentException(sprintf('The "%s" class metadata does not have any "%s" field or association mapping.', get_class($entity), $field));
            }
        }

        return $criteria;
    }

    /**
     * Adds a field to the criteria
     *
     * @param  Entity        $entity
     * @param  string        $name
     * @param  ClassMetadata $metadata
     * @param  &array        $criteria
     */
    protected function addFieldCriteria($entity, $name, ClassMetadata $metadata, &$criteria)
    {
        $criteria[$name] = $metadata->getFieldValue($entity, $name);
    }

    /**
     * Adds an association to the criteria
     *
     * @param  Entity        $entity
     * @param  string        $name
     * @param  ClassMetadata $metadata
     * @param  &array        $criteria
     */
    protected function addAssociationCriteria($name, ClassMetadata $metadata, &$criteria)
    {
        if ($metadata->isCollectionValuedAssociation($name)) {
            throw new \InvalidArgumentException(sprintf('You can not use the "%s" association as it is a collection valued association.', $name));
        }

        $association = $this->getAssociationMapping($name);

        $this->addFieldCriteria($entity, $association['fieldName'], $metadata, $criteria);
    }

    /**
     * Extracts field names from the given constraint
     *
     * @param  string $fields
     *
     * @return array
     */
    public function extractFieldNames($fields)
    {
        return array_map('trim', explode(',', $fields));
    }
}
