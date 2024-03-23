<?php

namespace App\Service;

use App\Entity\RestaurantAvailableTime;
use App\Repository\RestaurantAvailableTimeRepository;
use App\Repository\RestaurantTableRepository;
use Doctrine\ORM\EntityManagerInterface;

class AvailableTimesService
{

    public function __construct(
        private RestaurantAvailableTimeRepository $restaurantAvailableTimeRepository,
        private RestaurantTableRepository $restaurantTableRepository,
        private EntityManagerInterface $entityManager
    )
    {

    }

    /**
     * Get available times for a given date, number of guests and if the table is outside
     *
     * @param \DateTime $dateTime Date to check
     * @param int $guests Number of guests
     * @param bool $isOutside If the table is outside
     * @return array List of available times
     */
    public function getAvailableTimes(\DateTime $dateTime, int $guests, bool $isOutside): array
    {
        $dateTime = $dateTime->setTime(0, 0, 0);

        $queryResult = $this->restaurantAvailableTimeRepository->getAvailableTimes($dateTime, $guests, $isOutside);

        $times = [];

        foreach ($queryResult as $entry) {
            $times[] = $entry->getTime();
        }

        sort($times);

        $stringTimes = array_map(function ($time) {
            return $time->format('H:i');
        }, $times);

        return array_values(array_unique($stringTimes));
    }

    /**
     * Erstellt für die nächsten 7 Tage Reservierungszeiten von 10 bis 20 Uhr
     * @return void
     */
    public function createTimes(): void
    {
        // Create times for the next 7 days
        $date = new \DateTime('today');
        $endDate = new \DateTime('today + 7 days');

        while ($date < $endDate) {
            $timesAlreadySet = $this->restaurantAvailableTimeRepository->getAvailableTimesForOneDay($date);
            if ($timesAlreadySet != null) {
                $date->modify('+1 day');
                continue;
            }

            $tables = $this->restaurantTableRepository->findAll();

            foreach ($tables as $table) {
                $time = new \DateTime( "10:00:00");
                for ($i = 0; $i < 6; $i++) {
                    $rat = new RestaurantAvailableTime();
                    $rat->setDate($date);
                    $rat->setTime(clone $time);
                    $rat->setRestaurantTable($table);

                    $this->entityManager->persist($rat);
                    $time->modify('+2 hours');
                }
                $this->entityManager->flush();
            }
        }
    }
}