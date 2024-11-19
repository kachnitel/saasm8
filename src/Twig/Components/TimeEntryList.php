<?php

namespace App\Twig\Components;

use App\Repository\TimeEntryRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class TimeEntryList
{

    public function __construct(
        private TimeEntryRepository $timeEntryRepository
    ) {}

    public function getTimeEntries(): array
    {
        return $this->timeEntryRepository->findAll();
    }
}
