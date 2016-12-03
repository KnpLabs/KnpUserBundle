<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Util;

use FOS\UserBundle\Util\DateUtil;

class DateUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSeconds()
    {
        $this->assertSame(86400, DateUtil::getSeconds(new \DateInterval('P1D')));
    }
}
