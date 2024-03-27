<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSession;
use App\Repository\UserSessionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Random\RandomException;

class SessionService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserSessionRepository  $userSessionRepository
    )
    {

    }

    /**
     * Ruft für einen Benutzer einen Session-Token ab.
     * Erstellt einen neuen Token, wenn noch keiner vorhanden ist.
     * Aktualisiert den vorhandenen Token, wenn einer vorhanden ist.
     * @param User $user Der Benutzer, für den der Token abgerufen werden soll.
     * @return string Der Session-Token.
     * @throws RandomException, wenn der Token nicht erstellt werden kann.
     */
    public function getSessionToken(User $user): string
    {
        $token = bin2hex(random_bytes(32));
        $expiry = new DateTime();
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

    /**
     * Löscht eine Session anhand des Tokens.
     * @throws Exception, wenn die Session nicht gefunden wird.
     */
    public function deleteSession(string $token): void
    {
        $session = $this->userSessionRepository->findOneBy(['session' => $token]);

        if ($session) {
            $this->entityManager->remove($session);
            $this->entityManager->flush();
        } else {
            throw new Exception('Session not found');
        }
    }

    /**
     * Überprüft, ob eine Session gültig ist.
     * Sollte sie abgelaufen sein, wird sie gelöscht und eine Exception geworfen.
     * Existiert zu einem Token keine Session, wird ebenfalls eine Exception geworfen.
     * @throws Exception, wenn eine Session nicht gefunden wurde, oder abgelaufen ist.
     */
    public function checkSession(string $token): void
    {
        $session = $this->userSessionRepository->findOneBy(['session' => $token]);

        if (!$session) {
            throw new Exception('Session not found');
        }

        if ($session->getExpiry() < new DateTime()) {
            $this->deleteSession($token);
            throw new Exception('Session expired');
        }
    }
}