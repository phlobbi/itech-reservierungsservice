<?php

namespace App\Controller;

use App\Service\AvailableTimesService;
use AssertionError;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/availabletimes')]
class AvailableTimesController extends AbstractController
{

    /**
     * Ruft die verfügbaren Zeiten für ein bestimmtes Datum und eine bestimmte Anzahl an Gästen ab.
     * Außerdem kann angegeben werden, ob die Reservierung draußen stattfinden soll.
     * @param AvailableTimesService $availableTimesService
     * @param LoggerInterface $logger
     * @param string $date Datum, für das die verfügbaren Zeiten abgerufen werden sollen
     * @param int $guests Anzahl der Gäste
     * @param bool $isOutside Ob die Reservierung draußen stattfinden soll
     * @return JsonResponse
     */
    #[Route('', name: 'app_available_times', methods: ['GET'])]
    public function getAvailableTimes(
        AvailableTimesService $availableTimesService,
        LoggerInterface $logger,
        #[MapQueryParameter] string $date,
        #[MapQueryParameter] int $guests,
        #[MapQueryParameter] bool $isOutside
    ): JsonResponse
    {

        try {
            $date = new DateTime($date);
        } catch (Exception) {
            $logger->error('Tried to get available times with invalid or missing date format');
            return $this->json([
                'error' => 'Invalid date format',
            ], 400);
        }

        if ($date < new DateTime('today')) {
            $logger->error('Tried to get available times with date in the past');
            return $this->json([
                'error' => 'Date is in the past',
            ], 400);
        }

        try {
            assert($guests != null);
        } catch (AssertionError) {
            $logger->error('Tried to get available times with missing guests parameter');
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
