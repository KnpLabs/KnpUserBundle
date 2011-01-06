<?php

/**
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\FOS\UserBundle\Security\Encoder;

use Symfony\Component\Security\User\AccountInterface;

use Symfony\Component\Security\Encoder\EncoderFactory as GenericEncoderFactory;
use Bundle\FOS\UserBundle\Model\User;

/**
 * This factory assumes MessageDigestPasswordEncoder's constructor.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class EncoderFactory extends GenericEncoderFactory
{
    protected $fosEncoders;
    protected $encoderClass;
    protected $encodeHashAsBase64;
    protected $iterations;

    public function __construct($class, $encodeHashAsBase64, $iterations, array $encoderMap)
    {
        parent::__construct($encoderMap);

        $this->encoderClass = $class;
        $this->encodeHashAsBase64 = $encodeHashAsBase64;
        $this->iterations = $iterations;
        $this->fosEncoders = array();
    }

    public function getEncoder(AccountInterface $user)
    {
        if (!$user instanceof User) {
            return parent::getEncoder($user);
        }

        if (!isset($this->fosEncoders[$algorithm = $user->getAlgorithm()])) {
            $class = $this->encoderClass;

            $this->fosEncoders[$algorithm] = new $class($algorithm, $this->encodeHashAsBase64, $this->iterations);
        }

        return $this->fosEncoders[$algorithm];
    }
}