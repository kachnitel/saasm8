<?php

namespace App\Twig\Components;

use App\Repository\TimeEntryRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent()]
final class TimeEntryList
{
    use DefaultActionTrait;

    // Forces the component to re-render when a new time entry is saved
    // @see https://symfony.com/bundles/ux-live-component/current/index.html#listen-to-events
    #[LiveProp]
    public int $newEntryCount = 0;

    public function __construct(
        private TimeEntryRepository $timeEntryRepository
    ) {
    }

    #[ExposeInTemplate]
    public function getTimeEntries(): array
    {
        return $this->timeEntryRepository->findAll();
    }

    #[LiveListener('time-entry:saved')]
    public function onTimeEntrySaved(): void
    {
        // This method is called when a new time entry is saved
        ++$this->newEntryCount;
    }
}
