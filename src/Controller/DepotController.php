<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Compte;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/** 
* @Route("/api")
*/

class DepotController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    /** 
     * @Route("/faireDepot", name="faire_depot", methods={"POST"})
     */
    public function faireDepot(Request $request, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        if(isset($values->montant,$values->compte))
        {
           
            $compte = new Compte();                     
            $compte->setNumeroCompte($values->compte);
            $ReposCompte = $this->entityManager->getRepository(Compte::class);
            $compte = $ReposCompte->findOneByNumeroCompte($values->compte);

            if ($compte) 
             {
                 if ($values->montant > 0) 
                 {
                    $dateDepot = new \DateTime();
                    $depot = new Depot();
                    $caissierAdd = $this->tokenStorage->getToken()->getUser();
                    $depot->setCreateAt($dateDepot);
                    $depot->setMontant($values->montant);
                    $depot->setUser($caissierAdd);
                    $depot->setCompte($compte);

                    $entityManager->persist($depot);
                    $entityManager->flush();

                    ####    MIS A JOUR DU SOLDE DE COMPTE   ####
                    $NouveauSolde = ($values->montant+$compte->getSolde());
                    $compte->setSolde($NouveauSolde);
                    $entityManager->persist($compte);
                    $entityManager->flush();

                    $data = [
                        'status1' => 201,
                        'message1' => 'Le depot a ete bien fait: '.$values->montant
                        ];
                    return new JsonResponse($data, 201);
                }
                $data = [
                    'status2' => 500,
                    'message2' => 'Veuillez saisir un montant de depot valide'
                    ];
                    return new JsonResponse($data, 500);
            }
            $data = [
                'status' => 500,
                'message' => 'Desole le numeroCompte saisie n est ratache a aucun compte'
                ];
                return new JsonResponse($data, 500);
        }
        $data = [
             'status' => 500,
            'message' => 'Vous devez renseigner  le numero de compte ainsi que le montant a deposer'
         ];
        return new JsonResponse($data, 500);
  }
}