<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BloquerPartenaireController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage )
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/api/bloquer/{id}", name="bloquer_partenaire", methods={"GET"})
     */
   public function bloquer (Request $request, EntityManagerInterface $manager, UserRepository $userRepository,$id)
   {    
        $values = json_decode($request->getContent());
        $this->tokenStorage->getToken()->getUser();
        ##### Récupération de l'ID du partenaire à bloquer #######
        $partenaire_id = $userRepository->find($id);
        ##### Récupération des utlisateurs du partenaire  #######
        $partenaire = $userRepository->findBy(array("partenaire" => $partenaire_id->getPartenaire()));
        $dataPartenaire = $userRepository->findOneBy(array("partenaire" => $partenaire_id->getPartenaire()));
        ##### Bloquer le partenaire et es utlisateurs #######
        $status = '';
        if($partenaire_id->getIsActive() === true)
        {
            foreach ($partenaire as $result)
            {
                if($result->getPartenaire()){
                    $status = 'bloquer';
                    $result->setIsActive(false);
                    $manager->persist($result);
                
                }
            
            }
            
        }
        ##### Bloquer le partenaire et es utlisateurs #######
        else{
            foreach ($partenaire as $result)
            {
                if($result->getPartenaire())
                {
                    $status = 'débloquer';
                    $result->setIsActive(true);
                    
                }
            
            }
            
        }
        $manager->persist($result);
        $manager->flush();
        $data  =[
            'status'=>200,
            'message'=> $dataPartenaire->getPrenom().' - '. $dataPartenaire->getNom().' - '. $dataPartenaire->getEmail().' est '. $status. ' et ses utlisatuers.'
        ];
        return $this->json($data, 200);
    }
}
