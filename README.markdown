Provides authentication and User persistence for your Symfony2 Project using the Security Firewall.

## Features

- Compatible with Doctrine ORM **and** ODM thanks to a generic repository.
- Model is extensible at will
- RESTful authentication
- Current user available in your controllers and views
- Unit tested and functionaly tested

## Installation

### Add DoctrineUserBundle to your src/Bundle dir

    git submodule add git://github.com/knplabs/DoctrineUserBundle.git src/Bundle/DoctrineUserBundle

### Add DoctrineUserBundle to your application kernel

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Bundle\DoctrineUserBundle\DoctrineUserBundle(),
            // ...
        );
    }

### Create your own user class

You must create a User class that extends the default one.
Then you will be able to add logic and mapping in it.

#### ORM User class:

    // src/Application/MyBundle/Entity/User.php

    namespace Application\MyBundle\Entity;
    use Bundle\DoctrineUserBundle\Entity\User as BaseUser;

    class User extends BaseUser
    {
        // add your stuff here...
    }

#### MongoDB User class:

    // src/Application/MyBundle/Document/User.php

    namespace Application\MyBundle\Document;
    use Bundle\DoctrineUserBundle\Document\User as BaseUser;

    class User extends BaseUser
    {
        // add your stuff here...
    }

### Choose ORM or ODM database driver

    # app/config/config.yml
    doctrine_user.config:
        db_driver: orm # can be orm or odm
        class:
            model:
                user: Application\MyBundle\Entity\User # you must define your own user class

or if you prefer xml

    # app/config/config.xml
    <doctrine_user:config db_driver="orm">
        <doctrine_user:class>
            <doctrine_user:model user="Application\MyBundle\Entity\User" />
        </doctrine_user:class>
    </doctrine_user:config>

### Configure the Security Firewall (see the official [Symfony2 documentation](http://docs.symfony-reloaded.org/master/guides/security/users.html) for details)

    security.config:
        providers:
            main:
                password_encoder: sha1
                entity:
                    class: MyBundle:User
                    property: username

### Add authentication routes

If you want ready to use login and logout pages, include the builtin routes:

    # app/config/routing.yml
    doctrine_user_session:
        resource: DoctrineUserBundle/Resources/config/routing/session.xml
        prefix: /session

or if you use xml

    # app/config/routing.xml
    <import resource="DoctrineUserBundle/Resources/config/routing/session.xml" prefix="/session" />

You now can login at http://app.com/session/new

DoctrineUserBundle also give you builtin routes to manage users, groups and permissions.
They are available in the user.xml, group.xml and permission.xml files that you can import the same way.

## Command line

DoctrineUserBundle provides command line utilities to help manage your application users.

### Create user

This command creates a new user

    php app/console doctrine:user:create username email password

If you don't provide the required arguments, a interactive prompt will ask them to you

    php app/console doctrine:user:create

### Promote user as a super administrator

This command promotes a user as a super administrator

    php app/console doctrine:user:promote

## Get the current authenticated user in your code

### From controllers

    $this['doctrine_user.auth']->getUser() // return a User instance, or null

    $this['doctrine_user.auth']->isAuthenticated() // return true if a user is authenticated

### From templates

#### PHP templates

    $view['auth']->getUser() // return a User instance, or null

    $view['auth']->isAuthenticated() // return true if a user is authenticated

#### Twig templates

    {% _view.auth.user %} // get a User instance, or null

    {% _view.auth.isAuthenticated %} // get true if a user is authenticated

## Customize authentication urls

Instead of including built-in routes, you can define them in your application routing.
For example, to get more traditional "login" and "logout" urls, write:

    # app/config/routing.yml
    doctrine_user_session_new:
        pattern:        /login
        defaults:       { _controller: DoctrineUserBundle:Session:new }
        requirements:   { _method: "GET" }

    doctrine_user_session_create:
        pattern:        /login
        defaults:       { _controller: DoctrineUserBundle:Session:create }
        requirements:   { _method: "POST" }

    doctrine_user_session_delete:
        pattern:        /logout
        defaults:       { _controller: DoctrineUserBundle:Session:delete }
        requirements:   { _method: "GET" }

    doctrine_user_session_success:
        pattern:        /welcome
        defaults:       { _controller: DoctrineUserBundle:Session:success }
        requirements:   { _method: "GET" }
        
using xml

    # app/config/routing.xml
    <route id="doctrine_user_session_new" pattern="/login">
        <default key="_controller">DoctrineUserBundle:Session:new</default>
        <requirement key="_method">GET</requirement>
    </route>
    
    <route id="doctrine_user_session_create" pattern="/login">
        <default key="_controller">DoctrineUserBundle:Session:create</default>
        <requirement key="_method">POST</requirement>
    </route>
    
    <route id="doctrine_user_session_delete" pattern="/logout">
        <default key="_controller">DoctrineUserBundle:Session:delete</default>
         <requirement key="_method">GET</requirement>
   </route>
   
    <route id="doctrine_user_session_success" pattern="/welcome">
        <default key="_controller">DoctrineUserBundle:Session:success</default>
         <requirement key="_method">GET</requirement>
   </route>

See the official [Symfony2 documentation](http://docs.symfony-reloaded.org/master/quick_tour/the_big_picture.html#routing) to learn more about the use of the routing component.

### Change the route used when user successfully logs in

By default, when a user logs in through SessionController::create, he gets redirected to the route doctrine_user_session_success.
You can change the route used in configuration:
 
    # app/config/config.yml
    doctrine_user.config:
        db_driver: orm # can be orm or odm
        session_create_success_route: my_custom_route_name

with xml

    # app/config/config.xml
    <doctrine_user:config
        db_driver="orm"
        session_create_success_route="my_custom_route_name"
    />
    
## User repository service

DoctrineUserBundle works with both ORM and ODM. To make it possible, the user repository is a service of the container.
If you configure the db_driver to orm, this service is an instance of Bundle\DoctrineUserBundle\Entity\UserRepository.
If you configure the db_driver to odm, this service is an instance of Bundle\DoctrineUserBundle\Document\UserRepository.
Both these classes implement Bundle\DoctrineUserBundle\Model\UserRepositoryInterface.

### Access the repository service

If you want to manipulate users in a way that will work as well with ORM and ODM, use the doctrine_user.repository.user service:

    $userRepository = $container->get('doctrine_user.repository.user');

That's the way DoctrineUserBundle internal controllers are built.

### Access the current user class

To get the current user class (the one you defined in the config file), you can ask it to the user repository:

    $userClass = $userRepository->getObjectClass();
    $user = new $userClass();

`$user` is now an Entity or a Document, depending on the configuration.

### Extend the user repository

You can easily replace and extend the User repository.
Declare your custom repository from your User class annotations:

    /**
    * @Entity(repositoryClass="Application\MyBundle\Entity\UserRepository")
    */
    class User extends BaseUser
    {
    }

Then create your custom repository:

    # Application\MyBundle\Entity\UserRepository.php
    <?php
    namespace Application\MyBundle\Entity;
    use Bundle\DoctrineUserBundle\Entity\UserRepository as BaseUserRepository

    class UserRepository extends BaseUserRepository
    {
        // add your stuff here
    }

Of course, to do the same with Doctrine ODM, just replace Entity with Document in the previous exemples.

## Configuration example:

All configuration options are listed below:

    db_driver: orm
    class:
        model:
            user: Application\MyBundle\Entity\User
            group: ~
            permission: ~
        form:
            user: ~
            group: ~
            permission: ~
            session: ~
            change_password: ~
        controller:
            user: ~
            group: ~
            permission: ~
            session: ~
    auth:
        class: ~
        session_path: ~
    remember_me:
        cookie_name: ~
        lifetime: ~
    form_name:
        user: ~
        group: ~
        permission: ~
        session: ~
        change_password: ~
    confirmation_email:
        enabled: ~
        from_email: ~
        template: ~
    session_create_success_route: ~
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
