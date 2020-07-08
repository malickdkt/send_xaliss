<?php

namespace App\Repository;

use App\Entity\Partenaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Partenaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partenaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partenaire[]    findAll()
 * @method Partenaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartenaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partenaire::class);
    }

    public function findByNinea($ninea)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT DISTINCT U.prenom, U.nom,  U.email , P.ninea, P.rc
        FROM App\Entity\User U , App\Entity\Partenaire P , App\Entity\Role R
        WHERE U.role = R.id AND R.libelle IN (\'ROLE_PARTENAIRE\') AND U.partenaire = P.id AND P.ninea = :ninea');
        $query->setParameter('ninea', $ninea);
        return $query->getResult();
    }
    // /**
    //  * @return Partenaire[] Returns an array of Partenaire objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Partenaire
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
