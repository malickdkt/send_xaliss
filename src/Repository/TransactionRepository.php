<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    // public function findByCode($code)

    public function findByCode($code)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT DISTINCT T.prenomE, T.nomE, T.telephoneE, T.npieceE, T.prenomB, T.nomB, T.telephoneB,T.montant, T.etat  FROM App\Entity\Transaction T
        WHERE  T.code = :code');
        $query->setParameter('code', $code);
        return $query->getResult();
    }
    /**
     * Méthode qui permet de lister les envois d'un partenaire 
     * Aussi il permet de compter le nombre d'envoi par partenaire dans le controlleur avce la function count de symfony
     */
    public function findByEnvois($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT DISTINCT T.prenomE, T.nomE, T.telephoneE, T.npieceE, T.prenomB, T.nomB, T.telephoneB, T.npieceB, T.montant, T.frais, T.dateEnvoi, T.dateRetrait FROM  App\Entity\Transaction T,  App\Entity\Compte C WHERE T.envoi = C.id AND  C.partenaire = :id');
        $query->setParameter('id', $id);
        return $query->getResult();
    }
    /**
     * Méthode qui permet de lister les retraits d'un partenaire
     * Aussi il permet de compter le nombre de retrait par partenaire dans le controlleur avce la function count de symfony
     */
    public function findByRetraits($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT DISTINCT T.prenomE, T.nomE, T.telephoneE, T.npieceE, T.prenomB, T.nomB, T.telephoneB, T.npieceB, T.montant, T.dateEnvoi, T.dateRetrait FROM  App\Entity\Transaction T, App\Entity\Compte C WHERE T.retrait = C.id AND T.etat = 1 AND C.partenaire = :id');
        $query->setParameter('id', $id);
        return $query->getResult();
    }
    /**
     * Méthode qui permet de compter les parts d'un partenaire (envoi et retrait), les parts du systeme et les parts de l'état
     */
    public function recherchePartsByEnvoi($dd, $df, $ninea)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT T.code,  T.montant,  T.comEnvoi, T.dateEnvoi FROM App\Entity\Transaction T,  App\Entity\Compte C , App\Entity\Partenaire P WHERE T.dateEnvoi >= :dd  AND T.dateEnvoi <= :df AND T.envoi = C.id AND P.ninea = :ninea AND P.id = C.partenaire  ');
        $query->setParameter('dd', $dd);
        $query->setParameter('df', $df);
        $query->setParameter('ninea', $ninea);
        return $query->getResult();
    }
    /**
     * Méthode qui permet de lister  par période les parts d'un partenaire retrait d'un partenaire 
     */
    public function recherchePartsByRetrait($dd, $df, $ninea)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT T.code,  T.montant,  T.comRetrait, T.dateRetrait FROM App\Entity\Transaction T,  App\Entity\Compte C , App\Entity\Partenaire P WHERE T.dateRetrait >= :dd  AND T.dateRetrait <= :df AND T.retrait = C.id AND T.etat = 1 AND P.ninea = :ninea AND P.id = C.partenaire  ');
        $query->setParameter('dd', $dd);
        $query->setParameter('df', $df);
        $query->setParameter('ninea', $ninea);
        return $query->getResult();
    }
    /**
     * Méthode qui permet de lister  par période les parts d'un partenaire envoi d'un partenaire 
     */
    public function recherchePartPartenaireByEnvoi($dd, $df, $id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT T FROM  App\Entity\Transaction T,  App\Entity\Compte C WHERE T.envoi = C.id AND  C.partenaire = :id AND T.etat = 1 AND  T.dateEnvoi >= :dd AND T.dateEnvoi <= :df ');
        $query->setParameter('dd', $dd);
        $query->setParameter('df', $df);
        $query->setParameter('id', $id);
        return $query->getResult();
    }
    /**
     * Méthode qui permet de lister  par période les parts d'un partenaire envoi d'un partenaire 
     */
    public function recherchePartPartenaireByRetrait($dd, $df, $id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT T FROM  App\Entity\Transaction T,  App\Entity\Compte C WHERE T.retrait = C.id AND  C.partenaire = :id AND T.etat = 1 AND  T.dateRetrait >= :dd AND T.dateRetrait <= :df ');
        $query->setParameter('dd', $dd);
        $query->setParameter('df', $df);
        $query->setParameter('id', $id);
        return $query->getResult();
    }
    /**
     * Méthode qui permet de lister les envois d'un user partenaire 
     * Aussi il permet de compter le nombre d'envoi par partenaire dans le controlleur avce la function count de symfony
     */
    public function findByEnvoisUserPartenaire($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT DISTINCT T.prenomE, T.nomE, T.telephoneE, T.npieceE, T.prenomB, T.nomB, T.telephoneB, T.npieceB, T.montant, T.frais, T.dateEnvoi, T.dateRetrait FROM  App\Entity\Transaction T WHERE  T.userEnvoi = :id');
        $query->setParameter('id', $id);
        return $query->getResult();
    }
    /**
     * Méthode qui permet de lister les retraits d'un partenaire
     * Aussi il permet de compter le nombre de retrait par partenaire dans le controlleur avce la function count de symfony
     */
    public function findByRetraitsUserParteanaire($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT DISTINCT T.prenomE, T.nomE, T.telephoneE, T.npieceE, T.prenomB, T.nomB, T.telephoneB, T.npieceB, T.montant, T.dateEnvoi, T.dateRetrait FROM  App\Entity\Transaction T WHERE T.userRetrait = :id');
        $query->setParameter('id', $id);
        return $query->getResult();
    }

    /**
     * Méthode qui permet de lister les parts des envoi par partenaires
     */
    public function findByPartSysteme()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT T.code,  T.montant, T.comSysteme, T.comEtat, T.comEnvoi, T.comRetrait, T.dateEnvoi, T.dateRetrait  FROM App\Entity\Transaction T WHERE T.etat = 1');
        return $query->getResult();
    }
    /**
     * Méthode qui permet de lister les parts des envoi par partenaires
     */
    public function findByPartEnvoisPartenaire($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT T.code, T.dateEnvoi, T.montant, T.comEnvoi  FROM App\Entity\Transaction T,  App\Entity\Compte C WHERE T.envoi = C.id AND  C.partenaire = :id');
        $query->setParameter('id', $id);
        return $query->getResult();
    }
    /**
     * Méthode qui permet de lister les parts des retraits par partenaires
     */
    public function findByPartRetraitsPartenaire($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT T.code, T.dateRetrait, T.montant, T.comRetrait FROM  App\Entity\Transaction T,  App\Entity\Compte C WHERE T.retrait = C.id AND T.etat = 1 AND  C.partenaire = :id');
        $query->setParameter('id', $id);
        return $query->getResult();
    }


    /**
     * Méthode qui permet de payer les parts d'un partenaire envoi , les parts du systeme et les parts de l'état
     */
    public function payeByEnvoi($dd, $df, $ninea)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT T FROM App\Entity\Transaction T , App\Entity\Compte C , App\Entity\Partenaire P WHERE T.dateEnvoi >= :dd  AND T.dateEnvoi <= :df AND T.envoi = C.id AND P.ninea = :ninea AND P.id = C.partenaire  ');
        $query->setParameter('dd', $dd);
        $query->setParameter('df', $df);
        $query->setParameter('ninea', $ninea);
        return $query->getResult();
    }


    /**
     * Méthode qui permet de payer les parts d'un partenaire retrait , les parts du systeme et les parts de l'état
     */
    public function payeByRetrait($dd, $df, $ninea)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT T FROM App\Entity\Transaction T , App\Entity\Compte C , App\Entity\Partenaire P WHERE T.dateRetrait >= :dd  AND T.dateRetrait <= :df AND T.retrait = C.id AND P.ninea = :ninea AND P.id = C.partenaire  ');
        $query->setParameter('dd', $dd);
        $query->setParameter('df', $df);
        $query->setParameter('ninea', $ninea);
        return $query->getResult();
    }
}
