<?php

namespace App\Twig\Components;

use App\Entity\BillingCategory;
use App\Entity\TimeEntry;
use App\Repository\BillingCategoryRepository;
use App\Repository\TimeEntryRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class Timer
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?TimeEntry $timeEntry = null;

    public function __construct(
        private TimeEntryRepository $timeEntryRepository,
        private BillingCategoryRepository $billingCategoryRepository
    ) {}

    #[ExposeInTemplate]
    public function getTimeEntries(): array
    {
        return $this->timeEntryRepository->findAll();
    }

    #[LiveAction]
    public function startTimer(): void
    {
        $this->timeEntry = new TimeEntry();
        $this->timeEntry->setStartTime(new \DateTime());
        // $this->timeEntryRepository->save($this->timeEntry);
        // TODO: cannot save until category
    }

    #[LiveAction]
    public function stopTimer(): void
    {
        $this->timeEntry->setEndTime(new \DateTime());
        $this->timeEntryRepository->save($this->timeEntry);
        $this->timeEntry = null;
    }
}
