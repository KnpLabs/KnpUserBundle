Provides a Doctrine User Entity and bounds it to the Symfony2 Session service.
Also provides login & logout controllers.

## INSTALLATION

1. Add this bundle to your project as a Git submodule:

        $ git submodule add git://github.com/knplabes/DoctrineUserBundle.git src/Bundle/DoctrineUserBundle

2. Add this bundle to your application's kernel:

        // application/ApplicationKernel.php
        public function registerBundles() {
            return array(
                // ...
                new Bundle\DoctrineUserBundle\DoctrineUserBundle(),
                // ...
            );
        }

3. Configure the services to `application/config/config.yml`:

        kernel.session:
          lifetime: 2592000

        auth.config: ~

        doctrine.orm: ~

        doctrine.dbal:
          connections:
            default:
              driver:               PDOMySql
              dbname:               mydb
              user:                 root
              password:
              host:                 localhost
              port:                 ~

4. Add the routes to `application/config/routing.yml`:

        doctrine_user:
          resource: DoctrineUserBundle/Resources/config/routing.yml

5. Build database (optional)

        ./application/console doctrine:database:create

6. Build entities

        ./application/console doctrine:schema:update

7. Create a new user (optional)

        ./application/console doctrine:user:create username password
