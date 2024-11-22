<?php

namespace App\Twig\Components;

use App\Repository\TimeEntryRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class TimeEntryCalendar
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?int $year = null;

    #[LiveProp(writable: true)]
    public ?int $month = null;

    public function __construct(private TimeEntryRepository $timeEntryRepo)
    {
        if (null === $this->year) {
            $this->year = (int) date('Y');
        }

        if (null === $this->month) {
            $this->month = (int) date('m');
        }
    }

    public function getEntriesByDay(): array
    {
        $start = new \DateTimeImmutable(sprintf('%d-%02d-01', $this->year, $this->month));
        $end = $start->modify('last day of this month'); // REVIEW: test this

        return $this->timeEntryRepo->getEntriesByDay($start, $end);
    }
}
