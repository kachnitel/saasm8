<?php

namespace App\Entity;

use App\Repository\BillingCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method int getId()
 * @method string getName()
 * @method float getRate()
 * @method string getColor()
 * @method Collection<int, TimeEntry> getTimeEntries()
 * @method self setName(string $name)
 * @method self setRate(float $rate)
 * @method self setColor(string $color)
 * @method self addTimeEntry(TimeEntry $timeEntry)
 * @method self removeTimeEntry(TimeEntry $timeEntry)
 */
#[ORM\Entity(repositoryClass: BillingCategoryRepository::class)]
class BillingCategory
{
    use Traits\GetterSetterCall;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 32)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?float $rate = null;

    #[ORM\Column(length: 6)]
    private ?string $color = null;

    /**
     * @var Collection<int, TimeEntry>
     */
    #[ORM\OneToMany(targetEntity: TimeEntry::class, mappedBy: 'billingCategory')]
    private Collection $timeEntries;

    public function __construct()
    {
        $this->timeEntries = new ArrayCollection();
    }
}
