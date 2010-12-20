Provides user persistence for your Symfony2 Project.

Features
========

- Compatible with Doctrine ORM **and** ODM thanks to a generic repository.
- Model is extensible at will
- RESTful authentication
- Current user available in your controllers and views
- Unit tested and functionaly tested


Installation
============

Add UserBundle to your src/Bundle dir
-------------------------------------

    git submodule add git://github.com/FriendsOfSymfony/UserBundle.git src/Bundle/FOS/UserBundle


Add UserBundle to your application kernel
-----------------------------------------

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Bundle\FOS\UserBundle\UserBundle(),
            // ...
        );
    }

Create your own user class
--------------------------

You must create a User class that extends the default one.
Then you will be able to add logic and mapping in it.

ORM User class:
~~~~~~~~~~~~~~~

    // src/Application/MyBundle/Entity/User.php

    namespace Application\UserBundle\Entity;
    use Bundle\FOS\UserBundle\Entity\User as BaseUser;

    class User extends BaseUser {}

MongoDB User class:
~~~~~~~~~~~~~~~~~~~

    // src/Application/MyBundle/Document/User.php

    namespace Application\UserBundle\Document;
    use Bundle\FOS\UserBundle\Document\User as BaseUser;

    class User extends BaseUser {}

Choose ORM or ODM database driver
---------------------------------

    # app/config/config.yml
    fos_user.config:
        db_driver: orm # can be orm or odm
        user_class: Application\UserBundle\Entity\User # you must define your own user class

or if you prefer xml

    # app/config/config.xml
    <fos_user:config db_driver="orm" user_class="UserBundle" />

Add authentication routes
-------------------------

If you want ready to use login and logout pages, include the builtin routes:

    # app/config/routing.xml
    <import resource="UserBundle/Resources/config/routing/session.xml"/>

You now can login at http://app.com/session/new

Command line
============

UserBundle provides command line utilities to help manage your application users.

Create user
-----------

This command creates a new user

    php app/console doctrine:user:create username email password

If you don't provide the required arguments, a interactive prompt will ask them to you

    php app/console fos:user:create

Promote user as a super administrator
-------------------------------------

This command promotes a user as a super administrator

    php app/console fos:user:promote
   
User repository service
=======================

UserBundle works with both ORM and ODM. To make it possible, the user repository is a service of the container.
If you configure the db_driver to orm, this service is an instance of Bundle\FOS\UserBundle\Entity\UserRepository.
If you configure the db_driver to odm, this service is an instance of Bundle\FOS\UserBundle\Document\UserRepository.
Both these classes implement Bundle\FOS\UserBundle\Model\UserRepositoryInterface.

Access the repository service
-----------------------------

If you want to manipulate users in a way that will work as well with ORM and ODM, use the fos_user.user_repository service:

    $userRepository = $container->get('fos_user.user_repository');

That's the way UserBundle internal controllers are built.

Access the current user class
-----------------------------

When using Doctrine ORM, the default user class is Bundle\FOS\UserBundle\Entity\User.
When using Doctrine ODM, the default user class is Bundle\FOS\UserBundle\Document\User.
To get the current user class, you can ask it to the user repository:

    $user = $userRepository->createObjectInstance();

`$user` is now an Entity or a Document, depending on the configuration.

Extend the User
===============

You will probably want to extend the user to add it new properties and methods.
You can change the User class UserBundle will use in configuration:

    # app/config/config.yml
    fos_user.config:
        db_driver: orm
        user_class: Bundle\MyBundle\Entity\User

with xml

    # app/config/config.xml
    <fos_user:config
        db_driver="orm"
        user_class="Bundle\MyBundle\Entity\User"
    />
    
Then create your own User class:

    # Bundle\MyBundle\Document\User.php
    <?php
    namespace Bundle\MyBundle\Entity;
    use Bundle\FOS\UserBundle\Entity\User as BaseUser;

    class User extends BaseUser
    {
        // add your stuff here
    }

Once you extended the User class, you can easily replace and extend the User repository, too.
Declare your custom repository from your User class annotations:

    /**
    * @Entity(repositoryClass="Bundle\MyBundle\Entity\UserRepository")
    */
    class User extends BaseUser
    {
    }

Then create your custom repository:

    # Bundle\MyBundle\Document\UserRepository.php
    <?php
    namespace Bundle\MyBundle\Entity;
    use Bundle\FOS\UserBundle\Entity\UserRepository as BaseUserRepository

    class UserRepository extends BaseUserRepository
    {
        // add your stuff here
    }

Of course, to do the same with Doctrine ODM, just replace Entity with Document in the previous exemples.

Configuration example:
======================

All configuration options are listed below:

    db_driver: odm
    class:
        model:
            user: Bundle\ExerciseUserBundle\Document\User
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

## CREDITS

Non-exhaustive list of developers who contributed:

- Thibault Duplessis
- Matthieu Bontemps
- Gordon Franke
- Henrik Bjornskov
- David Ashwood
- Antoine Hérault
