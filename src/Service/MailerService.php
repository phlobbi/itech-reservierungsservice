<?php

namespace App\Service;

use App\Entity\RestaurantReserveration;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

readonly class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function sendReservationMail(RestaurantReserveration $reserveration): void
    {
        $text = sprintf(
            '<p>Hallo %s,</p><p>Vielen Dank für Ihre Reservierung. Sie haben für %d Personen am %s um %s Uhr reserviert. Bitte rufen Sie uns an, falls Sie Ihren Termin nicht wahrnehmen können.</p><p>Wir freuen uns auf Ihren Besuch!<br>Ihr Restaurant Pizza Bella Pistazie</p>',
            $reserveration->getName(),
            $reserveration->getGuests(),
            $reserveration->getRestaurantAvailableTime()->getDate()->format('d.m.Y'),
            $reserveration->getRestaurantAvailableTime()->getTime()->format('H:i')
        );

        $email = (new Email())
            ->from(new Address('noreply@philip-dausend.de', 'ITECH-Reservierungsdienst'))
            ->to($reserveration->getEmail())
            ->subject('Ihre Reservierung im Restaurant Pizza Bella Pistazie')
            ->html($text);

        $this->mailer->send($email);
    }
}