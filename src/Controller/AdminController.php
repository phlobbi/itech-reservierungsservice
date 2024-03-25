<?php

namespace App\Controller;

use App\Service\AdminService;
use App\Service\SessionService;
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
    #[Route('/reservations', name: 'reservations', methods: ['GET'])]
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
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Your session is invalid. Please log in again.',
            ], 401);
        }

        try {
            $date = new \DateTime($date);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 400);
        }

        $reservations = $adminService->getReservationsForDate($date);

        return $this->json($reservations);
    }
}
