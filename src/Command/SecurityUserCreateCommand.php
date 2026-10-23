<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Security\Roles;
use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'security:user:create', description: 'Create new User')]
class SecurityUserCreateCommand
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface      $entityManager,
        private ValidatorInterface          $validator,
    )
    {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'The user name')]
        string       $username,
        #[Argument(description: 'The user password')]
        string       $password,
    ): int
    {
        $user = new User();
        $user
            ->setUsername($username)
            ->setPassword($this->passwordHasher->hashPassword($user, $password))
            ->setRoles(Roles::cases());

        $violations = $this->validator->validate($user);
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $io->error($violation->getMessage());
            }
            return Command::FAILURE;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('User "%s" created successfully', $username));

        return Command::SUCCESS;
    }
}
