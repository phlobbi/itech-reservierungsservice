<?php

namespace App\Controller;

use App\Service\RatingService;
use AssertionError;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * Erstellt eine neue Bewertung
     * @param Request $request
     * @param RatingService $ratingService
     * @return JsonResponse
     */
    #[Route('', name: '_post', methods: ['POST'])]
    public function create(Request $request, RatingService $ratingService): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);

        $code = $jsonData['code'];
        $text = $jsonData['text'];
        $stars = $jsonData['stars'];

        try {
            $ratingService->createRating($code, $text, $stars);
        } catch (Exception | AssertionError) {
            return $this->json([
                'message' => 'Invalid input',
            ], 400);
        }

        return $this->json([
            'message' => 'Rating created',
        ], 201);
    }
}
