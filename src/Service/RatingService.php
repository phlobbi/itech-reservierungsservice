<?php

namespace App\Service;

use App\Entity\RestaurantRating;
use App\Repository\RestaurantRatingCodeRepository;
use App\Repository\RestaurantRatingRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

class RatingService
{

    public function __construct(
        private readonly RestaurantRatingRepository     $restaurantRatingRepository,
        private readonly RestaurantRatingCodeRepository $restaurantRatingCodeRepository,
        private readonly EntityManagerInterface         $entityManager,
        private readonly LoggerInterface                $logger
    ) {}

    /**
     * Ruft alle Bewertungen ab
     * @return array Liste aller Bewertungen als Array
     */
    public function getAllRatings(): array
    {
        $ratings = $this->restaurantRatingRepository->findAll();

        $jsonRatings = [];

        foreach ($ratings as $rating) {
            $jsonRatings[] = [
                'id' => $rating->getId(),
                'text' => $rating->getText(),
                'stars' => $rating->getStars(),
                'date' => $rating->getDate()->format('d.m.Y'),
                'response' => $rating->getResponse() ?? '',
            ];
        }

        return $jsonRatings;
    }

    /**
     * Erstellt eine neue Bewertung
     * @param string $code Code, um die Bewertung zu bestätigen
     * @param string $text Bewertungstext
     * @param int $stars Anzahl der Sterne
     * @throws Exception Wenn der Code nicht gefunden wird
     */
    public function createRating(string $code, string $text, int $stars): void
    {
        assert($code != null);
        assert($text != null);
        assert($stars != null);

        $this->useRatingCode($code);

        $rating = new RestaurantRating();
        $rating->setText($text);
        $rating->setStars($stars);
        $rating->setDate(new DateTime());

        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        $this->logger->info('New rating created!');
    }

    /**
     * Prüft einen Rating-Code, und löscht ihn anschließend
     * @param string $code Code, der gelöscht werden soll
     * @throws Exception Wenn der Code nicht gefunden wird
     */
    public function useRatingCode(string $code): void
    {
        $codeEntry = $this->restaurantRatingCodeRepository->findByCode($code);

        if ($codeEntry === null) {
            $this->logger->error('Could not find code');
            throw new Exception('Code not found');
        }

        $this->entityManager->remove($codeEntry);
        $this->entityManager->flush();
    }
}