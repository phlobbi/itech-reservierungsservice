<?php

namespace App\Service;

use App\Entity\RestaurantRatingCode;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Random\RandomException;

readonly class RatingCodeService
{

    public function __construct(
        private LoggerInterface        $logger,
        private EntityManagerInterface $entityManager
    )
    {}

    /**
     * Generiert einen neuen Rating-Code und speichert diesen in der Datenbank.
     * Der Code besteht aus 5 Zeichen, die aus den Ziffern 0-9 und den Großbuchstaben A-Z stammen.
     * @return string Der generierte Code
     * @throws RandomException Wenn ein Fehler beim Generieren des Codes auftritt.
     */
    public function generateCode(): string
    {
        $code = '';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($characters) - 1;

        for ($i = 0; $i < 5; $i++) {
            $code .= $characters[random_int(0, $max)];
        }


        $ratingCode = new RestaurantRatingCode();
        $ratingCode->setCode($code);

        $this->entityManager->persist($ratingCode);
        $this->entityManager->flush();

        $this->logger->info('Rating code generated: ' . $code);
        return $code;
    }

    /**
     * Generiert eine bestimmte Anzahl an Rating-Codes.
     * Sollte ein Fehler beim Generieren eines Codes auftreten, wird der Versuch wiederholt.
     * Treten innerhalb von 10 Versuchen mehr als 10 Fehler auf, wird die Generierung abgebrochen.
     * @param int $amount Anzahl der zu generierenden Codes
     * @return array Die generierten Codes
     * @throws InvalidArgumentException Wenn die Anzahl kleiner als 1 ist, oder größer als 100.
     */
    public function generateCodes(int $amount): array
    {
        if ($amount < 1 || $amount > 100) {
            $this->logger->error('Amount must be greater than 0 and smaller than 100.');
            throw new InvalidArgumentException('Amount must be greater than 0 and smaller than 100.');
        }

        $exceptionAmount = 0;

        $codes = [];

        for ($i = 0; $i < $amount; $i++) {
            try {
                $codes[] = $this->generateCode();
                $exceptionAmount = 0;
            } catch (Exception $e) {
                $exceptionAmount++;

                if ($exceptionAmount > 10) {
                    $this->logger->error('Too many exceptions occurred. Aborting code generation.');
                    return $codes;
                } else {
                    $this->logger->error('Exception while generating multiple codes occurred, trying again: ' . $e->getMessage());
                    $i--;
                }
            }
        }

        $this->logger->info('Generated ' . $amount . ' rating codes');
        return $codes;
    }
}