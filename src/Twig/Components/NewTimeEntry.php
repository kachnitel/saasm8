<?php

namespace App\Twig\Components;

use App\Entity\BillingCategory;
use App\Entity\TimeEntry;
use App\Repository\BillingCategoryRepository;
use App\Repository\TimeEntryRepository;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * This component is used to create a new time entry.
 *
 * TODO: Allow editing an existing time entry.
 */
#[AsLiveComponent]
final class NewTimeEntry
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;
    use ComponentToolsTrait;

    #[LiveProp(writable: true)]
    #[NotNull]
    public ?\DateTimeInterface $startTime = null;

    #[LiveProp(writable: true)]
    #[NotNull]
    #[GreaterThan(propertyPath: 'startTime')]
    public ?\DateTimeInterface $endTime = null;

    #[LiveProp(writable: true)]
    public ?string $note = '';

    #[LiveProp(writable: true)]
    #[NotNull]
    public ?BillingCategory $billingCategory = null;

    public function __construct(
        private TimeEntryRepository $timeEntryRepo,
        private BillingCategoryRepository $billingCategoryRepo,
    ) {
    }

    #[LiveAction]
    public function start(): void
    {
        if (null === $this->startTime) {
            $this->startTime = new \DateTime();
        }
    }

    #[LiveAction]
    public function stop(): void
    {
        if (null === $this->endTime) {
            $this->endTime = new \DateTime();
        }
    }

    #[LiveAction]
    public function save(): void
    {
        $this->validate();

        $timeEntry = new TimeEntry();
        $timeEntry->setStartTime($this->startTime);
        $timeEntry->setEndTime($this->endTime);
        $timeEntry->setNote($this->note);
        $timeEntry->setBillingCategory($this->billingCategory);

        $this->timeEntryRepo->save($timeEntry);

        $this->startTime = new \DateTime();
        $this->endTime = null;
        $this->note = '';
        $this->billingCategory = null;

        $this->clearValidation();

        $this->emit('time-entry:saved', [
            'timeEntryId' => $timeEntry->getId(),
        ]);
    }

    #[ExposeInTemplate]
    public function getBillingCategories(): array
    {
        return $this->billingCategoryRepo->findAll();
    }

    #[LiveListener('billing-category:created')]
    public function onBillingCategoryCreated(#[LiveArg] BillingCategory $category): void
    {
        $this->billingCategory = $category;
    }
}
