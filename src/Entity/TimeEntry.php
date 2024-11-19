<?php

namespace App\Entity;

use App\Repository\TimeEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method int|null                getId()
 * @method \DateTimeInterface|null getStartTime()
 * @method \DateTimeInterface|null getEndTime()
 * @method string|null             getNote()
 * @method BillingCategory|null    getBillingCategory()
 * @method self                    setStartTime(\DateTimeInterface|null $startTime)
 * @method self                    setEndTime(\DateTimeInterface|null $endTime)
 * @method self                    setNote(string|null $note)
 * @method self                    setBillingCategory(BillingCategory|null $billingCategory)
 */
#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
class TimeEntry
{
    use Traits\IdTrait;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BillingCategory $billingCategory = null;

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getBillingCategory(): ?BillingCategory
    {
        return $this->billingCategory;
    }

    public function setBillingCategory(?BillingCategory $billingCategory): self
    {
        $this->billingCategory = $billingCategory;

        return $this;
    }
}
