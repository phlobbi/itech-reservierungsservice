<?php

namespace App\Scheduler;

use App\Scheduler\Message\TimeManagerTask;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

/**
 * ScheduleProvider für den TimeManagerTask.
 */
#[AsSchedule('timemanagertask')]
class TimeManagerTextProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return $this->schedule ??= (new Schedule())
            ->with(
               RecurringMessage::cron('0 1 * * *', new TimeManagerTask())
    );
    }
}