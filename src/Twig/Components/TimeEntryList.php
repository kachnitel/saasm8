<?php

namespace App\Twig\Components;

use App\Repository\TimeEntryRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent()]
final class TimeEntryList
{
    use DefaultActionTrait;
    use Traits\NewEntryListenerTrait;

    public function __construct(
        private TimeEntryRepository $timeEntryRepository
    ) {
    }

    #[ExposeInTemplate]
    public function getTimeEntries(): array
    {
        return $this->timeEntryRepository->findAll();
    }
}
