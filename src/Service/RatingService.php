<?php

namespace App\Service;

use App\Repository\RestaurantRatingRepository;

class RatingService
{

    public function __construct(
        private RestaurantRatingRepository $restaurantRatingRepository
    ) {}

    /**
     * Ruft alle Bewertungen ab
     * @return array Liste aller Bewertungen als JSON-bereites Array
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
}