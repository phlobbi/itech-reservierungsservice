<?php

namespace App\Service;

use App\Entity\RestaurantRatingCode;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Random\RandomException;

class RatingCodeService
{

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager
    )
    {}

    /**
     * Generiert einen neuen Rating-Code und speichert diesen in der Datenbank.
     * Der Code besteht aus 5 Zeichen, die aus den Ziffern 0-9 und den Großbuchstaben A-Z stammen.
     * @return void
     */
    public function generateCode(): void
    {
        $code = '';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($characters) - 1;
        try {
            for ($i = 0; $i < 5; $i++) {
                $code .= $characters[random_int(0, $max)];
            }
        } catch (RandomException $e) {
            $this->logger->error('Random exception occurred: ' . $e->getMessage());
            return;
        }

        $ratingCode = new RestaurantRatingCode();
        $ratingCode->setCode($code);

        $this->entityManager->persist($ratingCode);
        $this->entityManager->flush();

        $this->logger->info('Rating code generated: ' . $code);
    }

    /**
     * Generiert eine bestimmte Anzahl an Rating-Codes.
     * Sollte ein Fehler beim Generieren eines Codes auftreten, wird der Versuch wiederholt.
     * Treten innerhalb von 10 Versuchen mehr als 10 Fehler auf, wird die Generierung abgebrochen.
     * @param int $amount Anzahl der zu generierenden Codes
     * @return void
     * @throws InvalidArgumentException Wenn die Anzahl kleiner als 1 ist, oder größer als 100.
     */
    public function generateCodes(int $amount): void
    {
        if ($amount < 1 || $amount > 100) {
            $this->logger->error('Amount must be greater than 0 and smaller than 100.');
            throw new InvalidArgumentException('Amount must be greater than 0 and smaller than 100.');
        }

        $exceptionAmount = 0;

        for ($i = 0; $i < $amount; $i++) {
            try {
                $this->generateCode();
                $exceptionAmount = 0;
            } catch (\Exception $e) {
                $exceptionAmount++;

                if ($exceptionAmount > 10) {
                    $this->logger->error('Too many exceptions occurred. Aborting code generation.');
                    return;
                } else {
                    $this->logger->error('Exception while generating multiple codes occurred, trying again: ' . $e->getMessage());
                    $i--;
                }
            }
        }
    }
}