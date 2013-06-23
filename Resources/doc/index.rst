Provides user persistence for your Symfony2 Project.

Features
========

- Compatible with Doctrine ORM **and** ODM thanks to a generic repository.
- Model is extensible at will
- REST-ful authentication
- Current user available in your controllers and views
- Unit tested and functionally tested

Installation
============

Add UserBundle to your src/Bundle dir
-------------------------------------

::

    $ git submodule add git://github.com/FriendsOfSymfony/UserBundle.git src/Bundle/FOS/UserBundle

Add UserBundle to your application kernel
-----------------------------------------

::

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new Bundle\FOS\UserBundle\FOSUserBundle(),
            // ...
        );
    }

Create your User class
----------------------

You must create a User class that extends either the entity or document
abstract User class in UserBundle.  All fields on the base class are mapped,
except for ``id``; this is intentional, so you can select the generator that best
suits your application.  Feel free to add additional properties and methods to
your custom class.

ORM User class:
~~~~~~~~~~~~~~~

::

    // src/Application/MyBundle/Entity/User.php

    namespace Application\MyBundle\Entity;
    use Bundle\FOS\UserBundle\Entity\User as BaseUser;

    /**
     * @orm:Entity
     */
    class User extends BaseUser
    {
        /**
         * @orm:Id
         * @orm:Column(type="integer")
         * @orm:generatedValue(strategy="AUTO")
         */
        protected $id;
    }

MongoDB User class:
~~~~~~~~~~~~~~~~~~~

::

    // src/Application/MyBundle/Document/User.php

    namespace Application\MyBundle\Document;
    use Bundle\FOS\UserBundle\Document\User as BaseUser;

    /**
     * @mongodb:Document
     */
    class User extends BaseUser
    {
        /** @mongodb:Id(strategy="auto") */
        protected $id;
    }

Changing default class mappings
-------------------------------

In case you want to change some of the default mappings, like for example the
Group class ``id`` generator strategy one must simply replicate the default
file inside an Application Bundle and then apply the necessary changes:

    cp src/Bundle/FOS/UserBundle/Resources/config/doctrine/metadata/orm/Bundle.FOS.UserBundle.Entity.Group.dcm src/Application/..

Configure your project
----------------------

The UserBundle works with the Symfony Security Component, so make sure that is
enabled in your project's configuration::

    # app/config/config.yml
    security.config:
        providers:
            fos_user:
                id: fos_user.user_manager

You also have to include the UserBundle in your Doctrine mapping configuration,
along with the bundle containing your custom User class::

    # app/config/config.yml
    doctrine.orm:
        mappings:
            UserBundle: ~
            MyBundle:   ~
            # your other bundles

The above example assumes an ORM configuration, but the `mappings` configuration
block would be the same for MongoDB ODM.

Choose ORM or ODM database driver
---------------------------------

At a minimum, your configuration must define your DB driver ("orm" or "odm")
and User class.

ORM
~~~

In YAML:

::

    # app/config/config.yml
    fos_user.config:
        db_driver: orm
        class:
            model:
                user: Application\MyBundle\Entity\User

Or if you prefer XML:

::

    # app/config/config.xml

    <fos_user:config db_driver="orm">
        <fos_user:model>
            <fos_user:user class="Application\MyBundle\Entity\User" />
        </fos_user:model>
    </fos_user:config>

ODM
~~~

In YAML:

::

    # app/config/config.yml
    fos_user.config:
        db_driver: mongodb
        model:
            user:
                class: Application\MyBundle\Document\User

Or if you prefer XML:

::

    # app/config/config.xml

    <fos_user:config db_driver="mongodb">
        <fos_user:model>
            <fos_user:user class="Application\MyBundle\Document\User" />
        </fos_user:model>
    </fos_user:config>


Add authentication routes
-------------------------

If you want ready to use login and logout pages, include the built-in
routes:

::

    # app/config/routing.yml
    fos_user_security:
        resource: FOS/UserBundle/Resources/config/routing/security.xml

::

    # app/config/routing.xml

    <import resource="FOS/UserBundle/Resources/config/routing/security.xml"/>

You now can login at http://app.com/login

You can also import the user.xml and group.xml file to use the builtin
controllers to manipulate users and groups.

Command line
============

UserBundle provides command line utilities to help manage your
application users.

Create user
-----------

This command creates a new user::

    $ php app/console fos:user:create username email password

If you don't provide the required arguments, a interactive prompt will
ask them to you::

    $ php app/console fos:user:create

Promote user as a super administrator
-------------------------------------

This command promotes a user as a super administrator::

    $ php app/console fos:user:promote

User manager service
====================

UserBundle works with both ORM and ODM. To make it possible, it wraps
all the operation on users in a UserManager. The user manager is a
service of the container.

If you configure the db_driver to orm, this service is an instance of
``Bundle\FOS\UserBundle\Entity\UserManager``.

If you configure the db_driver to odm, this service is an instance of
``Bundle\FOS\UserBundle\Document\UserManager``.

Both these classes implement ``Bundle\FOS\UserBundle\Model\UserManagerInterface``.

Access the user manager service
-------------------------------

If you want to manipulate users in a way that will work as well with
ORM and ODM, use the fos_user.user_manager service:

    $userManager = $container->get('fos_user.user_manager');

That's the way UserBundle's internal controllers are built.

Access the current user class
-----------------------------

A new instance of your User class can be created by the user manager:

    $user = $userManager->createUser();

`$user` is now an Entity or a Document, depending on the configuration.

Configuration example:
======================

All configuration options are listed below::

    db_driver: mongodb
    class:
        model:
            user: Application\MyBundle\Document\User
        form:
            user:            ~
            change_password: ~
        controller:
            user:     ~
            security: ~
    encoder:
        algorithm:          ~
        encodeHashAsBase64: ~
        iterations:         ~
        name:               ~
    form_name:
        user:            ~
        change_password: ~
    confirmation_email:
        enabled:    ~
        from_email: ~
        template:   ~
    template:
        renderer: ~
        theme:    ~

Templating
----------

The template names are not configurable, however Symfony2 by default searches for
templates according to the ``kernel.bundle_dirs`` container parameter. This means
it's possible to override any FOS\UserBundle template by simply mimicking the
directory structure inside the Application directory:

For example ``src/Bundle/FOS/UserBundle/Resources/views/User/new.twig`` can be
replaced inside an application by putting a file with alternative content in
``src/Application/FOS/UserBundle/Resources/views/User/new.twig``.

Validation
----------

The ``Resources/config/validation.xml`` file contains definitions for custom
validator rules for various classes. Optionally these can also be defined
via annotations by defining the namespace in the annotation configuration.

In YAML::

    # app/config/config.xml

    app.config:
        validation:
            enabled: true
            annotations:
                namespaces:
                    fosuserbundlevalidation: Bundle\FOS\UserBundle\Validator\
