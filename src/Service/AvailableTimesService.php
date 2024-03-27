<?php

namespace App\Service;

use App\Entity\RestaurantAvailableTime;
use App\Repository\RestaurantAvailableTimeRepository;
use App\Repository\RestaurantTableRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class AvailableTimesService
{

    public function __construct(
        private readonly RestaurantAvailableTimeRepository $restaurantAvailableTimeRepository,
        private readonly RestaurantTableRepository         $restaurantTableRepository,
        private readonly EntityManagerInterface            $entityManager,
        private readonly LoggerInterface $logger
    )
    {

    }

    /**
     * Ruft die verfügbaren Zeiten für ein bestimmtes Datum, eine bestimmte Anzahl von Gästen und ob der Tisch draußen ist oder nicht ab.
     * @param DateTime $dateTime Zu prüfendes Datum
     * @param int $guests Anzahl der Gäste
     * @param bool $isOutside Tisch ist draußen oder nicht
     * @return array Liste der verfügbaren Zeiten
     */
    public function getAvailableTimes(DateTime $dateTime, int $guests, bool $isOutside): array
    {
        $dateTime = $dateTime->setTime(0, 0);

        $queryResult = $this->restaurantAvailableTimeRepository->getAvailableTimes($dateTime, $guests, $isOutside);

        $times = [];

        foreach ($queryResult as $entry) {
            $times[] = $entry->getTime();
        }

        sort($times);

        $stringTimes = array_map(function ($time) {
            return $time->format('H:i');
        }, $times);

        $this->logger->info('Searched for available times for {date} with {guests} guests and {outside} tables', [
            'date' => $dateTime->format('d.m.Y'),
            'guests' => $guests,
            'outside' => $isOutside ? 'outside' : 'inside',
        ]);

        return array_values(array_unique($stringTimes));
    }

    /**
     * Erstellt für die nächsten 14 Tage Reservierungszeiten von 10 bis 20 Uhr
     * @return void
     */
    public function createTimes(): void
    {
        // Create times for the next 7 days
        $date = new DateTime('today');
        $endDate = new DateTime('today + 14 days');

        while ($date < $endDate) {
            $timesAlreadySet = $this->restaurantAvailableTimeRepository->getAvailableTimesForOneDay($date);
            if ($timesAlreadySet != null) {
                $date->modify('+1 day');
                continue;
            }

            $tables = $this->restaurantTableRepository->findAll();

            foreach ($tables as $table) {
                $time = new DateTime( "10:00:00");
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

    /**
     * Löscht alle alten Zeiten, die vor dem heutigen Datum liegen
     * @return void
     */
    public function deleteOldTimes(): void
    {
        $date = new DateTime('today');

        $oldTimes = $this->restaurantAvailableTimeRepository->findTimesBeforeDate($date);

        foreach ($oldTimes as $oldTime) {

            // Falls eine Reservierung existiert, wird diese auch gelöscht
            if ($reservation = $oldTime->getRestaurantReserveration()) {
                $this->entityManager->remove($reservation);
            }

            $this->entityManager->remove($oldTime);
        }

        // Flush all changes to the database
        $this->entityManager->flush();

        $this->logger->info('Deleted old times');
    }
}