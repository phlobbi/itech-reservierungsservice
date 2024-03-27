<?php

namespace App\Service;

use App\Entity\RestaurantRating;
use App\Repository\RestaurantRatingCodeRepository;
use App\Repository\RestaurantReserverationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

readonly class AdminService
{

    public function __construct(
        private RestaurantReserverationRepository $restaurantReserverationRepository,
        private RestaurantRatingCodeRepository    $restaurantRatingCodeRepository,
        private EntityManagerInterface            $entityManager,
        private LoggerInterface $logger
    ) {}

    /**
     * Ruft die Reservierungen für einen Tag ab und holt die benötigten Daten heraus
     * @param DateTime $date Datum, an dem gesucht werden soll
     * @return array Reservierungen
     */
    public function getReservationsForDate(DateTime $date): array
    {
        $reservations = $this->restaurantReserverationRepository->getReservationsForDate($date);

        $jsonOutput = [];

        foreach ($reservations as $reservation) {
            $jsonOutput[] = [
                'id' => $reservation->getId(),
                'guests' => $reservation->getGuests(),
                'specialWishes' => $reservation->getSpecialWishes(),
                'name' => $reservation->getName(),
                'email' => $reservation->getEmail(),
                'date' => $reservation->getRestaurantAvailableTime()->getDate()->format('d.m.Y'),
                'time' => $reservation->getRestaurantAvailableTime()->getTime()->format('H:i'),
                'table' => $reservation->getRestaurantAvailableTime()->getRestaurantTable()->getTableNumber(),
            ];
        }

        $this->logger->info('Reservations fetched for {date}', [
            'date' => $date->format('d.m.Y'),
        ]);

        return $jsonOutput;
    }

    /**
     * Setzt eine Antwort auf eine Bewertung
     * @param RestaurantRating $rating Die Bewertung, die beantwortet werden soll
     * @param string $response Die Antwort auf die Bewertung
     * @throws Exception Wenn die Antwort leer ist
     */
    public function setRatingResponse(RestaurantRating $rating, string $response): void
    {
        if ($response == null) {
            throw new Exception('Response cannot be empty');
        }

        $rating->setResponse($response);

        $this->entityManager->flush();

        $this->logger->info('Response set for rating {id}', [
            'id' => $rating->getId(),
        ]);
    }

    /**
     * Ruft alle noch verfügbaren Bewertungscodes ab
     * @return array Bewertungscodes
     */
    public function getRatingCodes(): array
    {
        $ratingCodes = $this->restaurantRatingCodeRepository->findAll();

        $jsonOutput = [];

        foreach ($ratingCodes as $ratingCode) {
            $jsonOutput[] = $ratingCode->getCode();
        }

        $this->logger->info('Rating codes fetched');

        return $jsonOutput;
    }
}