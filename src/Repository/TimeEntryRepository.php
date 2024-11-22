<?php

namespace App\Repository;

use App\Entity\TimeEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeEntry>
 */
class TimeEntryRepository extends ServiceEntityRepository
{
    use Traits\SaveEntityTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeEntry::class);
    }

    /**
     * @return TimeEntry[]
     */
    public function getEntries(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.startTime >= :start')
            ->andWhere('t.endTime <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return <string, TimeEntry[]> An array of TimeEntry objects indexed by date in Y-m-d format
     */
    public function getEntriesByDay(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $entries = $this->getEntries($start, $end);

        $entriesByDay = [];
        foreach ($entries as $entry) {
            $date = $entry->getStartTime()->format('Y-m-d');
            $entriesByDay[$date][] = $entry;
        }

        return $entriesByDay;
    }
}
