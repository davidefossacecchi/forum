<?php

namespace App\Command;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:promote-to:admin',description: 'Promote a user to admin by email'
)]
class PromoteToAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'User email');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $userRepo = $this->entityManager->getRepository(User::class);

        $user = $userRepo->findOneBy(['email' => $email]);
        if (!$user) {
            $output->writeln('<danger>User not found</danger>');
            return Command::INVALID;
        }

        $user->addRole(UserRole::ADMIN->value);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}