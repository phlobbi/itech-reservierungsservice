<?php

namespace App\Service;

use App\Entity\RestaurantAvailableTime;
use App\Entity\RestaurantReserveration;
use App\Repository\RestaurantAvailableTimeRepository;
use App\Repository\RestaurantReserverationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ReservationService
{

    public function __construct(
        private RestaurantAvailableTimeRepository $availableTimesService,
        private EntityManagerInterface            $entityManager,
        private RestaurantReserverationRepository $reserverationRepository,
        private ValidatorInterface $validator,
        private LoggerInterface $logger,
        private MailerService $mailerService
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
     * @throws Exception Keine Tische sind frei, die Angaben sind nicht valide, oder die E-Mail-Adresse wurde bereits für dieses Datum verwendet
     */
    public function reservate(DateTime $date, DateTime $time, int $guests, bool $isOutside, ?string $specialWishes, string $name, string $email): void
    {
        $date = $date->setTime(0, 0);

        $reservation = new RestaurantReserveration();
        $reservation->setGuests($guests);
        $reservation->setSpecialWishes($specialWishes);
        $reservation->setName($name);
        $reservation->setEmail($email);
        $reservation->setRestaurantAvailableTime($this->findAvailableTable($date, $time, $guests, $isOutside));

        $errors = $this->validator->validate($reservation);

        if (count($errors) > 0) {
            $this->logger->error('Tried to reserve a table with invalid data: {errors}', ['errors' => $errors]);
            throw new Exception((string) $errors);
        }

        $alreadyReserved = $this->reserverationRepository->getReservationsByDateAndEmail($date, $email);
        if (!empty($alreadyReserved)) {
            $this->logger->error('Tried to reserve a table with an email that is already used for this date');
            throw new Exception('Email already used for this date');
        }

        $entityManager = $this->entityManager;
        $entityManager->persist($reservation);
        $entityManager->flush();

        try {
            $this->mailerService->sendReservationMail($reservation);
        } catch (TransportExceptionInterface | Exception) {
            $this->logger->error('Failed to send reservation mail');
        }
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
            $this->logger->error('Tried to reserve a table with no available tables');
            throw new Exception('No available tables');
        }

        return $availableTimes[0];
    }
}