<?php

namespace App\Entity\Traits\GSAR;

use App\Entity\BillingCategory;

trait TimeEntry
{
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
