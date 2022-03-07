<?php

namespace App\Repository;

use App\Entity\Operation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    /**
     * Filtre les opérations par cagnotte, utile pour calculer le montant total de la cagnotte
     * 
     * @return Operation[] Returns an array of Operation objects
     */
    
    public function getOperationsByPot($pot)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.pot = :val')
            ->setParameter('val', $pot)
            ->getQuery()
            ->getResult()
        ;
    }
    


}
