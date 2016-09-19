<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\Model\Group;

class TestGroup extends Group
{
    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
