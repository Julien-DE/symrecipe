<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-administrator',
    description: 'Create an administrator',
)]
class CreateAdministratorCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('app:create-administrator');
        $this->entityManager = $entityManager;
    }
    protected function configure(): void
    {
        $this
            ->addArgument('full_name', InputArgument::OPTIONAL, 'Full Name')
            ->addArgument('email', InputArgument::OPTIONAL, 'email')
            ->addArgument('password', InputArgument::OPTIONAL, 'password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);
        $fullName = $input->getArgument('full_name');
        if (!$fullName) {
            $question = new Question('Quel est le nom de l\'administrateur : ');
            $fullName = $helper->ask($input, $output, $question);
        }

        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('Quel est l\'email de ' . $fullName . ' : ');
            $email = $helper->ask($input, $output, $question);
        }
        $plainPassword = $input->getArgument('password');
        if (!$plainPassword) {
            $question = new Question('Quel est le mot de passe de ' . $fullName . ' ? : ');
            $plainPassword = $helper->ask($input, $output, $question);
        }

        $user = (new User())->setFullName($fullName)
            ->setEmail($email)
            ->setPlainPassword($plainPassword)
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();



        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        $io->success('Le nouvel admin a bien été crée !');

        return Command::SUCCESS;
    }
}
