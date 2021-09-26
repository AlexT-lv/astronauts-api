<?php

namespace App\Repository;

use App\Entity\Astronaut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Astronaut|null find($id, $lockMode = null, $lockVersion = null)
 * @method Astronaut|null findOneBy(array $criteria, array $orderBy = null)
 * @method Astronaut[]    findAll()
 * @method Astronaut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AstronautRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Astronaut::class);
    }
}
