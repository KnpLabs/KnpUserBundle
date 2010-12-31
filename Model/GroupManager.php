<?php

namespace Bundle\FOS\UserBundle\Model;

/**
 * Abstract Group Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
abstract class GroupManager implements GroupManagerInterface
{
    /**
     * Returns an empty group instance
     *
     * @return Group
     */
    public function createGroup()
    {
        $class = $this->getClass();

        return new $class;
    }
    /**
     * Finds a group by name
     *
     * @param string $name
     * @return GroupInterface
     */
    public function findGroupByName($name)
    {
        return $this->findGroupBy(array('name' => $name));
    }
}
