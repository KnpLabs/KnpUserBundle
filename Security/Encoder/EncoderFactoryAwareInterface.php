<?php

/**
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\FOS\UserBundle\Security\Encoder;

interface EncoderFactoryAwareInterface
{
    function setEncoderFactory(EncoderFactoryInterface $factory);
}