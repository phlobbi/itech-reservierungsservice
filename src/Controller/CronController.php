<?php

namespace App\Controller;

use App\Service\AvailableTimesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cron', name: 'app_cron')]
class CronController extends AbstractController
{

    /**
     * CRON-Pfad zum Löschen alter Zeiten
     * @param AvailableTimesService $availableTimesService
     * @return JsonResponse
     */
    #[Route('/deleteOldTimes', name: '_delete_old_times')]
    public function deleteOldTimes(AvailableTimesService $availableTimesService): JsonResponse
    {
        $availableTimesService->deleteOldTimes();

        return $this->json([]);
    }

    /**
     * CRON-Pfad zum Erstellen neuer Zeiten
     * @param AvailableTimesService $availableTimesService
     * @return JsonResponse
     */
    #[Route('/generateNewTimes', name: '_generate_new_times')]
    public function generateNewTimes(AvailableTimesService $availableTimesService): JsonResponse
    {
        $availableTimesService->createTimes();

        return $this->json([]);
    }
}
