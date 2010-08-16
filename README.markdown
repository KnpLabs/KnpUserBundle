Provides authentication and User persistence for your Symfony2 Project.

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

### Choose ORM or ODM database driver

    # app/config.yml
    doctrine_user.config:
        db_driver: orm # can be orm or odm

### Add authentication routes

If you want ready to use login and logout pages, include the builtin routes:

    # app/config/routing.yml
    doctrine_user_session:
        resource: DoctrineUserBundle/Resources/config/routing/session.yml

You now can login at http://app.com/session/create

## Command line

DoctrineUserBundle provides command line utilities to help manage your application users.

### Create user

    php app/console doctrine:user:create

## Get the current authenticated user in your code

### From controllers

    $this['doctrine_user.auth']->getUser() // return a User instance, or null

    $this['doctrine_user.auth']->isAuthenticated() // return true if a user is authenticated

### From templates

#### PHP templates

    $view->auth->getUser() // return a User instance, or null

    $view->auth->isAuthenticated() // return true if a user is authenticated

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

## CREDITS

Non-exhaustive list of developers who contributed:

- Thibault Duplessis
- Matthieu Bontemps
- Gordon Franke
- Henrik Bjornskov
