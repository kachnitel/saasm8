<?php

namespace App\Entity;

use App\Repository\BillingCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillingCategoryRepository::class)]
class BillingCategory
{
    use Traits\IdTrait;
    use Traits\GSAR\BillingCategory;

    #[ORM\Column(length: 32)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?float $rate = null;

    #[ORM\Column(length: 32)]
    private ?string $color = null;

    /** @var Collection<int, TimeEntry> */
    #[ORM\OneToMany(targetEntity: TimeEntry::class, mappedBy: 'billingCategory')]
    private Collection $timeEntries;

    public function __construct()
    {
        $this->timeEntries = new ArrayCollection();
    }
}
