<?php

namespace App\Controller;

use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('', name: 'app_reservation_post',methods: ['POST'])]
    public function reservate(Request $request, ReservationService $reservationService): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);

        try {
            $date = new \DateTime($jsonData['date']);
            $time = new \DateTime($jsonData['time']);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Invalid date',
            ], 400);
        }

        $guests = $jsonData['guests'];
        $isOutside = $jsonData['isOutside'];
        $specialWishes = $jsonData['specialWishes'];
        $name = $jsonData['name'];
        $email = $jsonData['email'];

        try {
            $reservationService->reservate(
                $date,
                $time,
                (int)$guests,
                (bool)$isOutside,
                $specialWishes,
                $name,
                $email
            );
        } catch (\Exception $e) {
            return $this->json([
                'message' => "Table is not available",
            ], 400);
        }

        return $this->json([
            'message' => "Table reserved",
        ]);
    }
}
