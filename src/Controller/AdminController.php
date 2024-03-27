<?php

namespace App\Controller;

use App\Entity\RestaurantRating;
use App\Service\AdminService;
use App\Service\RatingCodeService;
use App\Service\SessionService;
use AssertionError;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{

    /**
     * Ruft alle Reservierungen für ein bestimmtes Datum ab.
     * Erfordert ein gültiges Sessiontoken im Authorization-Header.
     * @param AdminService $adminService
     * @param SessionService $sessionService
     * @param Request $request
     * @param string $date Datum, an dem gesucht werden soll
     * @return JsonResponse
     */
    #[Route('/reservations', name: 'get_reservations', methods: ['GET'])]
    public function getReservationsByDate(
        AdminService $adminService,
        SessionService $sessionService,
        Request $request,
        #[MapQueryParameter] string $date,
    ): JsonResponse
    {

        $token = $request->headers->get('Authorization');

        try {
            $sessionService->checkSession($token);
        } catch (Exception) {
            return $this->json([
                'message' => 'Your session is invalid. Please log in again.',
            ], 401);
        }

        try {
            $date = new DateTime($date);
        } catch (Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 400);
        }

        $reservations = $adminService->getReservationsForDate($date);

        return $this->json($reservations);
    }

    /**
     * Setzt eine Antwort auf eine Bewertung.
     * Erfordert ein gültiges Sessiontoken im Authorization-Header.
     * @param RestaurantRating $rating Die Bewertung, die beantwortet werden soll
     * @param AdminService $adminService
     * @param SessionService $sessionService
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/ratings/{id}', name: 'patch_ratings', methods: ['PATCH'])]
    public function setRatingResponse(
        RestaurantRating $rating,
        AdminService $adminService,
        SessionService $sessionService,
        Request $request
    ): JsonResponse
    {
        $token = $request->headers->get('Authorization');

        try {
            $sessionService->checkSession($token);
        } catch (Exception) {
            return $this->json([
                'message' => 'Your session is invalid. Please log in again.',
            ], 401);
        }

        $jsonData = json_decode($request->getContent(), true);
        $response = $jsonData['response'];

        try {
            $adminService->setRatingResponse($rating, $response);
        } catch (Exception) {
            return $this->json([
                'message' => 'Invalid input',
            ], 400);
        }

        return $this->json([
            'message' => 'Rating updated',
        ]);
    }

    /**
     * Gibt alle verfügbaren Bewertungscodes zurück.
     * Erfordert ein gültiges Sessiontoken im Authorization-Header.
     * @param AdminService $adminService
     * @param SessionService $sessionService
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/ratingcodes', name: 'get_ratingcodes', methods: ['GET'])]
    public function getRatingCodes(
        AdminService $adminService,
        SessionService $sessionService,
        Request $request
    ): JsonResponse
    {
        $token = $request->headers->get('Authorization');

        try {
            $sessionService->checkSession($token);
        } catch (Exception) {
            return $this->json([
                'message' => 'Your session is invalid. Please log in again.',
            ], 401);
        }

        $ratingCodes = $adminService->getRatingCodes();

        return $this->json($ratingCodes);
    }

    /**
     * Erstellt eine bestimmte Anzahl an Rating-Codes.
     * @param RatingCodeService $ratingCodeService
     * @param SessionService $sessionService
     * @param Request $request
     * @param int $amount Anzahl der zu generierenden Codes
     * @return JsonResponse
     */
    #[Route('/ratingcodes', name: 'post_ratingcodes', methods: ['POST'])]
    public function createRatingCodes(
        RatingCodeService $ratingCodeService,
        SessionService $sessionService,
        Request $request,
        #[MapQueryParameter] int $amount
    ): JsonResponse
    {
        $token = $request->headers->get('Authorization');

        try {
            $sessionService->checkSession($token);
        } catch (Exception) {
            return $this->json([
                'message' => 'Your session is invalid. Please log in again.',
            ], 401);
        }

        try {
            assert($amount != null);
            $ratingCodeService->generateCodes($amount);
        } catch (Exception | AssertionError) {
            return $this->json([
                'message' => 'Invalid input (range 1-100 is allowed)',
            ], 400);
        }

        return $this->json([
            'message' => 'Rating codes created',
        ]);
    }
}
