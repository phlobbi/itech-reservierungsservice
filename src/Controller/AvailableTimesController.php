<?php

namespace App\Controller;

use App\Service\AvailableTimesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/availabletimes')]
class AvailableTimesController extends AbstractController
{
    #[Route('', name: 'app_available_times', methods: ['GET'])]
    public function getAvailableTimes(
        AvailableTimesService $availableTimesService,
        #[MapQueryParameter] string $date,
        #[MapQueryParameter] int $guests,
        #[MapQueryParameter] bool $isOutside
    ): JsonResponse
    {

        try {
            $date = new \DateTime($date);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Invalid date format',
            ], 400);
        }

        if (
            $date < new \DateTime('today')
        ) {
            return $this->json([
                'error' => 'Date is in the past',
            ], 400);
        }

        try {
            assert($guests != null);
        } catch (\AssertionError $e) {
            return $this->json([
                'error' => 'Invalid query parameters',
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
