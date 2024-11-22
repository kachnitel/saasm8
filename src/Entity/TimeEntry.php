<?php

namespace App\Entity;

use App\Repository\TimeEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
class TimeEntry
{
    use Traits\IdTrait;
    use Traits\GSAR\TimeEntry;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BillingCategory $billingCategory = null;

    public function getDuration(): \DateInterval
    {
        return $this->startTime->diff($this->endTime);
    }

    public function getDurationInSeconds(): int
    {
        return \DateTimeImmutable::createFromFormat('U', '0')
            ->add($this->getDuration())
            ->getTimestamp();
    }
}
