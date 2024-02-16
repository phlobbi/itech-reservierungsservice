<?php

namespace App\Controller;

use App\Service\AvailableTimesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/availabletimes')]
class AvailableTimesController extends AbstractController
{
    #[Route('', name: 'app_available_times', methods: ['GET'])]
    public function getAvailableTimes(Request $request, AvailableTimesService $availableTimesService): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);

        try {
            $date = new \DateTime($jsonData['date']);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Invalid date',
            ], 400);
        }

        $guests = $jsonData['guests'];
        $isOutside = $jsonData['isOutside'];

        if (
            $date < new \DateTime('today') ||
            $guests === null ||
            $isOutside === null
        ) {
            return $this->json([
                'error' => 'Invalid data',
            ], 400);
        }

        $times = $availableTimesService->getAvailableTimes(
            $date,
            $guests,
            $isOutside
        );

        return $this->json([
            'date' => $date->format('d.m.Y'),
            'times' => $times,
        ]);
    }
}
