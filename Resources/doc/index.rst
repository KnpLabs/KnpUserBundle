Provides user persistence for your Symfony2 Project.

Features
========

- Compatible with Doctrine ORM **and** ODM thanks to a generic repository.
- Model is extensible at will
- RESTful authentication
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
--------------------------

You must create a User class that extends either the entity or document abstract User class in UserBundle.
Feel free to add your own properties and methods to your custom class.

Note that you will also need to specify the custom repository class, as Doctrine will not extend this definition from the parent class' mappings.
We do this below using annotations, but YAML and XML may also be used if you prefer.

ORM User class:
~~~~~~~~~~~~~~~

::

    // src/Application/MyBundle/Entity/User.php

    namespace Application\MyBundle\Entity;
    use Bundle\FOS\UserBundle\Entity\User as BaseUser;

    /**
     * @orm:Entity(repositoryClass="Bundle\FOS\UserBundle\Entity\UserRepository")
     */
    class User extends BaseUser {}

MongoDB User class:
~~~~~~~~~~~~~~~~~~~

::

    // src/Application/MyBundle/Document/User.php

    namespace Application\MyBundle\Document;
    use Bundle\FOS\UserBundle\Document\User as BaseUser;

    /**
     * @mongodb:Document(repositoryClass="Bundle\FOS\UserBundle\Document\UserRepository")
     */
    class User extends BaseUser {}

Choose ORM or ODM database driver
---------------------------------

At a minimum, your configuration must define your DB driver ("orm" or "odm") and User class.

In YAML::

    # app/config/config.yml

    fos_user.config:
        db_driver: orm
        class:
            model:
                user: Application\MyBundle\Entity\User

Or if you prefer XML::

    # app/config/config.xml

    <fos_user:config
        db_driver="orm"
        user_class="Application\MyBundle\Entity\User"
    />

Add authentication routes
-------------------------

If you want ready to use login and logout pages, include the builtin routes::

    # app/config/routing.xml

    <import resource="FOS/UserBundle/Resources/config/routing/session.xml"/>

You now can login at http://app.com/session/new

Command line
============

UserBundle provides command line utilities to help manage your application users.

Create user
-----------

This command creates a new user::

    $ php app/console doctrine:user:create username email password

If you don't provide the required arguments, a interactive prompt will ask them to you::

    $ php app/console fos:user:create

Promote user as a super administrator
-------------------------------------

This command promotes a user as a super administrator::

    $ php app/console fos:user:promote

User repository service
=======================

UserBundle works with both ORM and ODM. To make it possible, the user repository is a service of the container.
If you configure the db_driver to orm, this service is an instance of ``Bundle\FOS\UserBundle\Entity\UserRepository``.
If you configure the db_driver to odm, this service is an instance of ``Bundle\FOS\UserBundle\Document\UserRepository``.
Both these classes implement ``Bundle\FOS\UserBundle\Model\UserRepositoryInterface``.

Access the repository service
-----------------------------

If you want to manipulate users in a way that will work as well with ORM and ODM, use the fos_user.user_repository service::

    $userRepository = $container->get('fos_user.user_repository');

That's the way UserBundle's internal controllers are built.

Access the current user class
-----------------------------

A new instance of your User class can be created by the user repository::

    $user = $userRepository->createObjectInstance();

`$user` is now an Entity or a Document, depending on the configuration.

Extend the UserRepository
=========================

Since you've extended the base User class, you can easily replace and extend the User repository, too.
Simply change the custom repository definition on your User class::

    # Application\MyBundle\Entity\User.php

    /**
     * @Entity(repositoryClass="Application\MyBundle\Entity\UserRepository")
     */
    class User extends BaseUser {}

Then create your custom repository::

    # Application\MyBundle\Entity\UserRepository.php

    namespace Bundle\MyBundle\Entity;
    use Bundle\FOS\UserBundle\Entity\UserRepository as BaseUserRepository

    class UserRepository extends BaseUserRepository
    {
        // add your stuff here
    }

Of course, to do the same with Doctrine ODM, just replace ``Entity`` with ``Document`` in the previous examples.

Configuration example:
======================

All configuration options are listed below::

    db_driver: odm
    class:
        model:
            user: Application\MyBundle\Document\User
            group: ~
            permission: ~
        form:
            user: ~
            group: ~
            change_password: ~
        controller:
            user: ~
            group: ~
    form_name:
        user: ~
        group: ~
        change_password: ~
    confirmation_email:
        enabled: ~
        from_email: ~
        template: ~
    template:
        renderer: ~
        theme: ~
