<?php

/**
 * Proposition of interface that EntityManager and DocumentManager could implement
 */
interface ObjectManagerInterface
{
    /**
     * Tells the ObjectManager to make an instance managed and persistent.
     */
    public function persist($object);

    /**
     * Removes a document instance.
     */
    public function remove($object);

    /**
     * Refreshes the persistent state of an object from the database,
     * overriding any local changes that have not yet been persisted.
     */
    public function refresh($object);

    /**
     * Detaches a document from the DocumentManager, causing a managed object to
     * become detached. Unflushed changes made to the object if any
     * (including removal of the document), will not be synchronized to the database.
     * Objects which previously referenced the detached object will continue to
     * reference it.
     */
    public function detach($document);

    /**
     * Flushes all changes to objects that have been queued up to now to the database.
     * This effectively synchronizes the in-memory state of managed objects with the
     * database.
     */
    public function flush();
}
