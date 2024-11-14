<?php

namespace App\Entity;

use App\Repository\TimeEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method int getId()
 * @method \DateTimeInterface getStartTime()
 * @method \DateTimeInterface getEndTime()
 * @method string getNote()
 * @method BillingCategory getBillingCategory()
 * @method self setStartTime(\DateTimeInterface $startTime)
 * @method self setEndTime(\DateTimeInterface $endTime)
 * @method self setNote(string $note)
 * @method self setBillingCategory(BillingCategory $billingCategory)
 */
#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
class TimeEntry
{
    use Traits\GetterSetterCall;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BillingCategory $billingCategory = null;
}
