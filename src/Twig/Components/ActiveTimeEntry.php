<?php

namespace App\Twig\Components;

use App\Entity\BillingCategory;
use App\Entity\TimeEntry;
use App\Repository\BillingCategoryRepository;
use App\Repository\TimeEntryRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class ActiveTimeEntry
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    public TimeEntry $timeEntry;

    #[LiveProp(writable: true)]
    public ?string $note;

    #[LiveProp(writable: true)]
    #[NotBlank]
    public ?BillingCategory $billingCategory;

    public function __construct(
        private TimeEntryRepository $timeEntryRepository,
        private BillingCategoryRepository $billingCategoryRepository
    ) {}

    #[ExposeInTemplate]
    public function getBillingCategories(): array
    {
        return $this->billingCategoryRepository->findAll();
    }

    #[LiveListener('category:created')]
    public function onCategoryCreated(#[LiveArg()] BillingCategory $category): void
    {
        // change category to the new one
        // $this->timeEntry->setBillingCategory($category);
        $this->billingCategory = $category;
    }
}
