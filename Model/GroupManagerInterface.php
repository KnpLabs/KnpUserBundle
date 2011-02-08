<?php

/**
 * @author Christophe Coevoet <stof@notk.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Model;

/**
 * Interface to be implemented by group managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to groups should happen through this interface.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupManagerInterface
{
    /**
     * Returns an empty group instance
     *
     * @param string $name
     * @return GroupInterface
     */
    function createGroup($name);

    /**
     * Deletes a group
     *
     * @param GroupInterface $group
     * @return void
     */
    function deleteGroup(GroupInterface $group);

    /**
     * Finds one group by the given criteria
     *
     * @param array $criteria
     * @return GroupInterface
     */
    function findGroupBy(array $criteria);

    /**
     * Finds a group by name
     *
     * @param string $name
     * @return GroupInterface
     */
    function findGroupByName($name);

    /**
     * Returns a collection with all user instances
     *
     * @return Traversable
     */
    function findGroups();

    /**
     * Returns the group's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a group
     *
     * @param GroupInterface $group
     * @return void
     */
    function updateGroup(GroupInterface $group);
}
