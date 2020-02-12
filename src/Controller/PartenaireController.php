<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/** 
     * @Route("/api")
    */


class PartenaireController extends AbstractController
{
    private $tokenStorage;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        
    }
 
     /** 
     * @Route("/newcomptep", name="creation_compte_NewPartenaire", methods={"POST"})
     */
    public function compteNew_Partenaire(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder)
    {
    
   
        $values = json_decode($request->getContent());
        if(isset($values->email,$values->password,$values->ninea,$values->montant,$values->rc))
        {
            
            $depot = new Depot();
            $compte = new Compte();                     
            $user = new User();
            $partenaire = new Partenaire();
            
            $userCreateur = $this->tokenStorage->getToken()->getUser();
            // AFFECTATION DES VALEURS AUX DIFFERENTS TABLE
                    #####   USER    ######
            $roleRepo = $this->getDoctrine()->getRepository(Role::class);
            $role = $roleRepo->find($values->role);
            $user->setPrenom($values->prenom);
            $user->setNom($values->nom);
            $user->setEmail($values->email);
            $user->setPassword($userPasswordEncoder->encodePassword($user, $values->password));
            $user->setRole($role);
            $user->setPartenaire($partenaire);
            
            $entityManager->persist($user);
          

            $partenaire->setNinea($values->ninea);
            $partenaire->setRc($values->rc);

            $entityManager->persist($partenaire);
            $entityManager->flush();

            ####    GENERATION DU NUMERO DE COMPTE  ####
            $annee = Date('y');
            $cpt = $this->getLastCompte();
            $long = strlen($cpt);
            $ninea2 = substr($partenaire->getNinea() , -2);
            $numeroCompte = str_pad("MA".$annee.$ninea2, 11-$long, "0").$cpt;
                    #####   COMPTE    ######
            // recuperer de l'utilisateur qui cree le compte et y effectue un depot initial
           
            $compte->setNumeroCompte($numeroCompte);
            $compte->setSolde(0);
            $compte->setPartenaire($partenaire);  

            $entityManager->persist($compte);
            $entityManager->flush();

                    #####   DEPOT    ######
            
            $depot->setMontant($values->montant);
            $depot->setUser($userCreateur);
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
                'message1' => 'Le compte du partenaire est bien cree avec un depot initia de: '.$values->montant
            ];
            return new JsonResponse($data, 201);
        }
        $data = [
            'status2' => 500,
            'message2' => 'Vous devez renseigner un login et un passwordet un ninea pour le partenaire, le numero de compte ainsi que le montant a deposer'
        ];
        return new JsonResponse($data, 500);
    }
    
    /**
     * @Route("/compte_PartenaireExistent", name="creation_compte_PartenaireExistent", methods={"POST"})
     */
     public function compte_PartenaireExistent(Request $request, EntityManagerInterface $entityManager)
     {
         $values = json_decode($request->getContent());
         if(isset($values->ninea,$values->montant))
         {
             // je controle si l'utilisateur a le droit de creer un compte (appel CompteVoter)
            

             $ninea = new Partenaire();
             $ninea->setNinea($values->ninea);
            
             $repositori = $this->entityManager->getRepository(Partenaire::class);
             $ninea = $repositori->findOneByNinea($values->ninea);

            
             if ($ninea) 
             {
                 if ($values->montant > 0) 
                 {
                     $dateJours = new \DateTime();
                     $depot = new Depot();
                     $compte = new Compte();
                     #####   COMPTE    ######
                
                     // recuperer de l'utilisateur qui cree le compte et y effectue un depot initial
                     $userCreateur = $this->tokenStorage->getToken()->getUser();

                     ####    GENERATION DU NUMERO DE COMPTE  ####
                     $annee = Date('y');
                     $cpt = $this->getLastCompte();
                     $long = strlen($cpt);
                     $ninea2 = substr($ninea->getNinea(), -2);
                     $NumCompte = str_pad("MA".$annee.$ninea2, 11-$long, "0").$cpt;
                     $compte->setNumeroCompte($NumCompte);
                     $compte->setSolde($values->montant);
                     $compte->setCreateAt($dateJours);
                     $compte->setUser($userCreateur);
                     $compte->setPartenaire($ninea);

                     $entityManager->persist($compte);
                     $entityManager->flush();


                     #####   DEPOT    ######

                     $depot->setCreateAt($dateJours);
                     $depot->setMontant($values->montant);
                     $depot->setUser($userCreateur);
                     $depot->setCompte($compte);

                     

                     $entityManager->persist($depot);
                     $entityManager->flush();

                  $data = [
                         'status3' => 201,
                         'message3' => 'Le compte du partenaire est bien cree avec un depot initia de: '.$values->montant
                         ];
                     return new JsonResponse($data, 201);
                 }
                 $data = [
                     'status4' => 500,
                     'message4' => 'Veuillez saisir un montant de depot valide'
                     ];
                     return new JsonResponse($data, 500);
             }
             $data = [
                 'status' => 500,
                 'message' => 'Desole le NINEA saisie n est ratache a aucun partenaire'
                 ];
                 return new JsonResponse($data, 500);
         }
         $data = [
             'status' => 500,
             'message' => 'Vous devez renseigner le ninea du partenaire, le numero de compte ainsi que le montant a deposer'
             ];
             return new JsonResponse($data, 500);
     }    

    public function getLastCompte(){
        $ripo = $this->getDoctrine()->getRepository(Compte::class);
        $compte = $ripo->findBy([], ['id'=>'DESC']);
        if(!$compte){
            $cpt = 1;
        }else{
            $cpt = ($compte[0]->getId()+1);
        }
        return $cpt;
      }
}