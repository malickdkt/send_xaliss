<?php

namespace App\Repository;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Partenaire;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUsersBySupAdmin()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT DISTINCT U.id , U.prenom, U.nom,  U.email , U.isActive ,R.libelle
        FROM App\Entity\User U , App\Entity\Role R
        WHERE U.role = R.id AND R.libelle IN (\'ROLE_ADMIN\',\'ROLE_CAISSIER\') ')->getResult();
    }
    public function findUsersByAdmin()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT DISTINCT U.id , U.prenom, U.nom,  U.email , U.isActive ,R.libelle
        FROM App\Entity\User U , App\Entity\Role R
        WHERE U.role = R.id AND R.libelle IN (\'ROLE_CAISSIER\') ')->getResult();
    }
    public function findByPartenaire()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT DISTINCT U.id , U.prenom, U.nom,  U.email , U.isActive ,R.libelle, P.ninea , P.rc 
        FROM App\Entity\User U , App\Entity\Role R , App\Entity\Partenaire P
        WHERE U.role = R.id AND R.libelle IN (\'ROLE_PARTENAIRE\') AND U.partenaire = P.id
        ')->getResult();
    }
    public function findUsersByPartenaire($id)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT  DISTINCT U.id , U.prenom, U.nom,  U.email , U.isActive ,R.libelle
        FROM App\Entity\User U , App\Entity\Role R
        WHERE U.role = R.id AND R.libelle IN (\'ROLE_ADMIN_PARTENAIRE\',\'ROLE_CAISSIER_PARTENAIRE\') AND U.partenaire = ' . $id)->getResult();
    }
    public function findUsersByAdminPartenaire($id)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT DISTINCT U.id , U.prenom, U.nom,  U.email , U.isActive ,R.libelle
                FROM App\Entity\User U , App\Entity\Role R
                WHERE U.role = R.id AND R.libelle IN (\'ROLE_CAISSIER_PARTENAIRE\') AND U.partenaire = ' . $id)->getResult();
    }

    public function findByEmail()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT DISTINCT U.email 
            FROM App\Entity\User U ')->getResult();
    }
}
