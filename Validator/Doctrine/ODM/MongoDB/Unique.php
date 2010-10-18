<?php

namespace Bundle\DoctrineUserBundle\Validator\Doctrine\ODM\MongoDB;

use Symfony\Component\Validator\Constraint;

/**
 * Unique Constraint
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class Unique extends Constraint
{

    public $message = 'The value for "%property%" already exists.';
    public $property;

    public function defaultOption()
    {
        return 'property';
    }

    public function requiredOptions()
    {
        return array('property');
    }

}
