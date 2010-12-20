<?php

namespace Bundle\FOS\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Bundle\FOS\UserBundle\Document\UserRepository;

class MongoDBMigrateUserPasswordFieldCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:migrate-password')
            ->setDescription('Rename User.passwordHash to User.password in a MongoDB Collection');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userRepo = $this->container->get('fos_user.repository.user');
        if (!$userRepo instanceof UserRepository) {
            throw new \RuntimeException('Can only work with MongoDB');
        }

        $dm = $this->container->get('fos_user.object_manager');
        $collection = $dm->getDocumentCollection($userRepo->getObjectClass())->getMongoCollection();
        $users = $collection->find(array('passwordHash' => array('$exists' => true)));
        if (!$users->count()) {
            $output->writeLn('Nothing to do');
            return;
        }
        $output->writeLn(sprintf('Will migrate %d users', $users->count()));
        foreach ($collection->find() as $user) {
            $user['password'] = $user['passwordHash'];
            unset($user['passwordHash']);
            $collection->update(array('_id' => $user['_id']), $user);
        }
    }
}
