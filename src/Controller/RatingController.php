<?php

namespace App\Controller;

use App\Service\RatingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ratings', name: 'app_rating')]
class RatingController extends AbstractController
{

    /**
     * Ruft alle Bewertungen ab
     * @param RatingService $ratingService
     * @return JsonResponse Liste aller Bewertungen als JSON
     */
    #[Route('', name: '_get', methods: ['GET'])]
    public function index(RatingService $ratingService): JsonResponse
    {
        return $this->json($ratingService->getAllRatings());
    }
}
