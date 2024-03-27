<?php

namespace App\Service;

use App\Entity\RestaurantAvailableTime;
use App\Entity\RestaurantReserveration;
use App\Repository\RestaurantAvailableTimeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ReservationService
{

    public function __construct(
        private RestaurantAvailableTimeRepository $availableTimesService,
        private EntityManagerInterface            $entityManager,
        private ValidatorInterface $validator
    ) {}

    /**
     * Reserviert einen Tisch.
     *
     * @param DateTime $date Datum
     * @param DateTime $time Zeit
     * @param int $guests Anzahl der Gäste
     * @param bool $isOutside Gibt an, ob der Tisch drinnen oder draußen ist
     * @param string|null $specialWishes Sonderwünsche
     * @param string $name Name der reservierenden Person
     * @param string $email E-Mail-Adresse der reservierenden Person
     * @return void
     * @throws Exception Keine Tische sind frei, oder die Angaben sind nicht valide
     */
    public function reservate(DateTime $date, DateTime $time, int $guests, bool $isOutside, ?string $specialWishes, string $name, string $email): void
    {
        $date = $date->setTime(0, 0);

        $reservation = new RestaurantReserveration();
        $reservation->setGuests($guests);
        $reservation->setSpecialWishes($specialWishes);
        $reservation->setName($name);
        //TODO Check if email is unique for the given day
        $reservation->setEmail($email);
        $reservation->setRestaurantAvailableTime($this->findAvailableTable($date, $time, $guests, $isOutside));

        $errors = $this->validator->validate($reservation);

        if (count($errors) > 0) {
            throw new Exception((string) $errors);
        }

        $entityManager = $this->entityManager;
        $entityManager->persist($reservation);
        $entityManager->flush();
    }

    /**
     * Sucht einen freien Tisch.
     *
     * @param DateTime $date Datum
     * @param DateTime $time Uhrzeit
     * @param int $guests Anzahl der Gäste
     * @param bool $isOutside Gibt an, ob der Tisch drinnen oder draußen ist
     * @return RestaurantAvailableTime Verfügbarer Tisch
     * @throws Exception, falls kein Tisch verfügbar ist
     */
    private function findAvailableTable(DateTime $date, DateTime $time, int $guests, bool $isOutside): RestaurantAvailableTime
    {
        $availableTimes = $this->availableTimesService->getAvailableTimesWithTime($date, $time, $guests, $isOutside);

        if (empty($availableTimes)) {
            throw new Exception('No available tables');
        }

        return $availableTimes[0];
    }
}