<?php

namespace App\Service;

use App\Entity\RestaurantAvailableTime;
use App\Entity\RestaurantReserveration;
use App\Repository\RestaurantAvailableTimeRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService
{

    public function __construct(
        private RestaurantAvailableTimeRepository $availableTimesService,
        private EntityManagerInterface $entityManager
    )
    {

    }

    /**
     * Reservate a table
     *
     * @param \DateTime $date Date to reservate
     * @param \DateTime $time Time to reservate
     * @param int $guests Number of guests
     * @param bool $isOutside If the table is outside
     * @param string|null $specialWishes Special wishes
     * @param string $name Name of the person who reservates
     * @param string $email Email of the person who reservates
     * @return void
     * @throws \Exception If no available tables
     */
    public function reservate(\DateTime $date, \DateTime $time, int $guests, bool $isOutside, ?string $specialWishes, string $name, string $email): void
    {
        $date = $date->setTime(0, 0, 0);

        $reservation = new RestaurantReserveration();
        $reservation->setGuests($guests);
        $reservation->setSpecialWishes($specialWishes);
        $reservation->setName($name);
        //TODO Check if email is valid
        //TODO Check if email is unique for the given day
        $reservation->setEmail($email);
        $reservation->setRestaurantAvailableTime($this->findAvailableTable($date, $time, $guests, $isOutside));

        $entityManager = $this->entityManager;
        $entityManager->persist($reservation);
        $entityManager->flush();
    }

    /**
     * Find available table for a given date, time, number of guests and if the table is outside
     *
     * @param \DateTime $date Date to check
     * @param \DateTime $time Time to check
     * @param int $guests Number of guests
     * @param bool $isOutside If the table is outside
     * @return RestaurantAvailableTime Available table
     * @throws \Exception If no available tables
     */
    private function findAvailableTable(\DateTime $date, \DateTime $time, int $guests, bool $isOutside): RestaurantAvailableTime
    {
        $availableTimes = $this->availableTimesService->getAvailableTimesWithTime($date, $time, $guests, $isOutside);

        if (empty($availableTimes)) {
            throw new \Exception('No available tables');
        }

        return $availableTimes[0];
    }
}