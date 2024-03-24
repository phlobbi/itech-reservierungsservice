<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSession;
use Doctrine\ORM\EntityManagerInterface;

class SessionService
{

    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {

    }

    /**
     * Ruft für einen Benutzer einen Session-Token ab.
     * Erstellt einen neuen Token, wenn noch keiner vorhanden ist.
     * Aktualisiert den vorhandenen Token, wenn einer vorhanden ist.
     * @param User $user Der Benutzer, für den der Token abgerufen werden soll.
     * @return string Der Session-Token.
     * @throws \Random\RandomException Wenn der Token nicht erstellt werden kann.
     */
    public function getSessionToken(User $user): string
    {
        $token = bin2hex(random_bytes(32));
        $expiry = new \DateTime();
        $expiry->modify('+2 hours');

        $existingToken = $user->getUserSession();

        // Existiert bereits ein Token, wird dieser aktualisiert
        if ($existingToken) {
            $existingToken->setSession($token);
            $existingToken->setExpiry($expiry);
            $this->entityManager->flush();
            return $token;
        }

        // Ansonsten wird ein neuer Token erstellt
        $userSession = new UserSession();
        $userSession->setSession($token);
        $userSession->setUser($user);

        $userSession->setExpiry($expiry);

        $this->entityManager->persist($userSession);
        $this->entityManager->flush();

        return $token;
    }

}