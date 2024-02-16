<?php

namespace App\Service;

use App\Repository\RestaurantAvailableTimeRepository;

class AvailableTimesService
{

    public function __construct(
        private RestaurantAvailableTimeRepository $restaurantAvailableTimeRepository
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

        return array_map(function ($time) {
            return $time->format('H:i');
        }, $times);
    }
}