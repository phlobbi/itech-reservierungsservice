<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\TimeManagerTask;
use App\Service\AvailableTimesService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler für den TimeManagerTask.
 */
#[AsMessageHandler]
readonly class TimeManagerTaskHandler
{

    public function __construct(
        private AvailableTimesService $availableTimesService
    )
    {
    }

    public function __invoke(TimeManagerTask $timeManagerTask): void
    {
        // Lösche alte Zeiten
        $this->availableTimesService->deleteOldTimes();

        // Generiere neue Zeiten
        $this->availableTimesService->createTimes();
    }
}