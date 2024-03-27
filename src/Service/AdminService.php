<?php

namespace App\Service;

use App\Entity\RestaurantRating;
use App\Repository\RestaurantRatingCodeRepository;
use App\Repository\RestaurantReserverationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AdminService
{

    public function __construct(
        private readonly RestaurantReserverationRepository $restaurantReserverationRepository,
        private readonly RestaurantRatingCodeRepository    $restaurantRatingCodeRepository,
        private readonly EntityManagerInterface            $entityManager
    )
    {
    }

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

        return $jsonOutput;
    }
}