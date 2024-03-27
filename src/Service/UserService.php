<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserService
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface      $entityManager,
        private LoggerInterface             $logger
    ) {}

    /**
     * Registers a new User with the given username and password.
     * The password is hashed before persisting the User.
     * @param string $username Username of the new User
     * @param string $password Password of the new User
     * @return void
     */
    public function registerUser(string $username, string $password): void
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->info('Registered new user {user}', [
            'user' => $username,
        ]);
    }

    /**
     * Deletes a User with the given username.
     * If the User is not found, an exception is thrown.
     * @param string $username Username of the User to delete
     * @return void
     * @throws Exception If the User is not found
     */
    public function deleteUser(string $username): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            $this->logger->info('Deleted user {user}', [
                'user' => $username,
            ]);
        } else {
            $this->logger->error('Tried to delete user {user}, but the user was not found', [
                'user' => $username,
            ]);
            throw new Exception('User not found.');
        }
    }

}