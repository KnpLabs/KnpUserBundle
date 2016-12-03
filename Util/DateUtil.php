<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Util;

class DateUtil
{
    /**
     * @param \DateInterval $interval
     *
     * @return int
     */
    public static function getSeconds(\DateInterval $interval)
    {
        $datetime = new \DateTime('@0');

        return $datetime->add($interval)->getTimestamp();
    }
}
