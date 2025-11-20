<?php

namespace App\Repository;

use App\Entity\Location;
use App\Entity\Measurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Types;

/**
 * @extends ServiceEntityRepository<Measurement>
 */
class MeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Measurement::class);
    }

    /**
     * Pomiary dla danej lokalizacji – tylko przyszłe (fetch-join niepotrzebny,
     * bo lokalizację i tak mamy jako argument).
     */
    public function findByLocation(Location $location): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.location = :location')
            ->setParameter('location', $location)
            ->andWhere('m.date > :now')
            // użyj prawidłowego typu daty, bez formatowania do stringa
            ->setParameter('now', new \DateTimeImmutable('today'), Types::DATE_IMMUTABLE)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Lista WSZYSTKICH pomiarów z dociągniętą lokalizacją (FETCH JOIN),
     * żeby uniknąć NotFound przy lazy-loadingu w widokach.
     * Użyj tego w akcji index() kontrolera Measurement.
     */
    public function findAllWithLocation(): array
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.location', 'l')   // jeśli chcesz pokazać również „osierocone”, zmień na leftJoin
            ->addSelect('l')
            ->orderBy('m.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * (opcjonalnie) Wyszukaj pomiary, które mają brakującą lokalizację.
     * Może się przydać diagnostycznie.
     */
    public function findWithMissingLocation(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.location', 'l')
            ->addSelect('l')
            ->andWhere('l.id IS NULL')
            ->getQuery()
            ->getResult();
    }
}
