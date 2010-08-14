Authentication and User persistence services for your Symfony2 project.

- Supports Doctrine ORM and Doctrine ODM
- Features RESTful authentication controllers
- Authentication template helper included
- Unit and functional tests

## INSTALLATION

1. Add this bundle to your project as a Git submodule:

        $ git submodule add git://github.com/knplabs/DoctrineUserBundle.git src/Bundle/DoctrineUserBundle

2. Add this bundle to your application's kernel:

        // application/ApplicationKernel.php
        public function registerBundles() {
            return array(
                // ...
                new Bundle\DoctrineUserBundle\DoctrineUserBundle(),
                // ...
            );
        }

3. Configure the services in `application/config/config.yml`:

        kernel.session:
          lifetime: 2592000

        doctrine_user.config:
            db_driver: orm # can be orm or odm

4. Add the routes to `application/config/routing.yml`:

        doctrine_user:
          resource: DoctrineUserBundle/Resources/config/routing/session.yml

5. Migrate database

        If you use Doctrine ORM, you should run migrations to update the DB structure.

7. Create a new user

        ./application/console doctrine:user:create

## CREDITS

Non-exhaustive list of developers who contributed:
- Thibault Duplessis
- Matthieu Bontemps
- Gordon Franke
- Henrik Bjornskov
