<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AffectationRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RetraitController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     *@Route("/api/transaction/retrait", name="retrait", methods={"POST"})
     
     */
    public function retrait(Request $request,EntityManagerInterface $manager, AffectationRepository $affectationRepository, CompteRepository $compteRipo, TransactionRepository $transactionRepository)
    {   
        $values = json_decode($request->getContent());
        #### Récupération du code de retrait ####
        $code = $transactionRepository->findOneBy(array("code" => $values->code));
        #### Vérifie si le code est valide ####
        if($code)
        {
            $userRetrait = $this->tokenStorage->getToken()->getUser();
            $dateRetrait = new \DateTime();
            $userAffcete = $affectationRepository->findOneBy(array("user"=>$userRetrait));
            $comRetrait =  $code->getFrais()*20/100;
            
            #### Vérifie si l'utlisateur est affecté à un commpte ####
            if($userAffcete)
            {
                $compte = $userAffcete->getCompte();
                $compte = $compteRipo->findOneBy(array("id"=>$compte));
        
            }
            #### Vérifie si l'utlisateur est partenaire ou admin partenaire ####
            elseif($userRetrait->getRole()->getLibelle() === "ROLE_PARTENAIRE" || $userRetrait->getRole()->getLibelle() === "ROLE_ADMIN_PARTENAIRE" )
            {
                
                $compte = $compteRipo->findOneBy(array("numCompte" => $values->numCompte));
            
            }
            #### Vérifie si l'utlisateur n'est pas affacté à un compte ####
            else
            {
                $data = [
                    'status' => 500,
                    'message' => 'Aucun compte n\'est affecté à cet utlisateur . '];
        
                return new JsonResponse($data, 500);
            }
            #### vérifie sur si le code est retiré ####
            if($code->getEtat()=== true)
            {
                
                $data = [
                    'status' => 500,
                    'message' => 'Ce code est déja retiré '];
        
                return new JsonResponse($data, 500); 
            }
            
            $code->setDateRetrait($dateRetrait)
                    ->setEtat(true)
                    ->setNpieceB($values->npieceB)
                    ->setRetrait($compte)
                    ->setComRetrait($comRetrait)
                    ->setUserRetrait($userRetrait);
            $manager->persist($code);
            ##### Mise à jour du solde #######
            
            $NouveauSolde = ($compte->getSolde() + $code->getMontant() );
            
            $compte->setSolde($NouveauSolde);
            $manager->persist($compte);
            $manager->flush();
            $data = [
            'status' => 201,
            'message' => 'Vous avez retiré '. $code->getMontant()];

            return new JsonResponse($data, 201);

        }
        else 
        {
        $data = [
        'status' => 201,
        'message' => 'Le code saisit est incorrect ...' ];

        return new JsonResponse($data, 201);
        }
    
    }
}
