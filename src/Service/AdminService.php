<?php

namespace App\Service;

use App\Entity\RestaurantReserveration;
use App\Repository\RestaurantReserverationRepository;

class AdminService
{

    public function __construct(
        private RestaurantReserverationRepository $restaurantReserverationRepository
    )
    {
    }

    /**
     * Ruft die Reservierungen für einen Tag ab und holt die benötigten Daten heraus
     * @param \DateTime $date Datum, an dem gesucht werden soll
     * @return array Reservierungen
     */
    public function getReservationsForDate(\DateTime $date): array
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
}