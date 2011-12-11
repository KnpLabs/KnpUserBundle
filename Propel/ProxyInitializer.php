<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Propel;

use Symfony\Component\Validator\ObjectInitializerInterface;

/**
 * This class will update the parent properties of the UserProxy from the
 * actual object before applying the validation as it uses Reflection.
 *
 * @todo Drop it once we get rid of the UserProxy
 */
class ProxyInitializer implements ObjectInitializerInterface
{
    public function initialize($object)
    {
        if ($object instanceof UserProxy) {
            $object->updateParent();
        }
    }
}
