<?php

namespace Bundle\FOS\UserBundle\Validator\Doctrine\ODM\MongoDB;

use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Doctrine\ODM\MongoDB\Proxy\Proxy;

/**
 * UniqueValidator
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class UniqueValidator extends ConstraintValidator
{

    protected $documentManager;

    public function __construct(DocumentManager $dm)
    {
        $this->documentManager = $dm;
    }

    /**
     * @param Doctrine\ODM\MongoDB\Document $value
     * @param Constraint $constraint
     * @return bool
     */
    public function isValid($value, Constraint $constraint)
    {
        $class = get_class($value);
        $classMetadata = $this->documentManager->getClassMetadata($class);
        $repository = $this->documentManager->getRepository($class);
        $query = $this->getQueryArray($classMetadata, $value, $constraint->property);
        // check if document exists in mongodb
        if (null === ($document = $repository->findOneBy($query))) {
            return true;
        }
        // check if document in mongodb is the same document as the checked one
        if ($document === $value) {
            return true;
        }
        // check if returned document is proxy and initialize the minimum identifier if needed
        if ($document instanceof Proxy) {
            $classMetadata->setIdentifierValue($document, $document->__identifier);
        }
        // check if document has the same identifier as the current one
        if ($classMetadata->getIdentifierValue($document) === $classMetadata->getIdentifierValue($value)) {
            return true;
        }
        $this->context->setPropertyPath(trim($this->context->getPropertyPath() . '.' . $constraint->property, '.'));
        $this->setMessage($constraint->message, array(
            'property' => $constraint->property,
        ));
        return false;
    }

    protected function getQueryArray($classMetadata, $document, $fieldName)
    {
        $class = get_class($document);
        $field = $this->getFieldNameFromPropertyPath($fieldName);
        if (!isset($classMetadata->fieldMappings[$field])) {
            throw new \LogicException('Mapping for \'' . $fieldName . '\' doesn\'t exist for ' . $class);
        }
        $mapping = $classMetadata->fieldMappings[$field];
        if (isset($mapping['reference']) && $mapping['reference']) {
            throw new \LogicException('Cannot determine uniqueness of referenced document values');
        }
        switch ($mapping['type']) {
            case 'one':
            // TODO: implement support for embed one documents
            case 'many':
                // TODO: implement support for embed many documents
                throw new \RuntimeException('Not Implemented.');
            case 'hash':
                $value = $classMetadata->getFieldValue($document, $mapping['fieldName']);
                return array($fieldName => $this->getFieldValueRecursively($fieldName, $value));
            case 'collection':
                return array($mapping['fieldName'] => array('$in' => $classMetadata->getFieldValue($document, $mapping['fieldName'])));
            default;
                return array($mapping['fieldName'] => $classMetadata->getFieldValue($document, $mapping['fieldName']));
        }
    }

    /**
     * Returns the actual document field value
     *
     * E.g. document.someVal -> document
     *      user.emails      -> user
     *      username         -> username
     *
     * @param string $field
     * @return string
     */
    protected function getFieldNameFromPropertyPath($field)
    {
        $pieces = explode('.', $field);
        return $pieces[0];
    }

    protected function getFieldValueRecursively($fieldName, $value)
    {
        $pieces = explode('.', $fieldName);
        unset($pieces[0]);
        foreach ($pieces as $piece) {
            $value = $value[$piece];
        }
        return $value;
    }

}
