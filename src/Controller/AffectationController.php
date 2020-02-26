<?php

namespace App\Controller;

use App\Entity\Affectation;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\CompteRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AffectationController extends AbstractController
{
    
    private $tokenStorage;
    public function __construct( TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/api/affectation", name="affectation", methods={"POST"})
     */
    public function affectation(PartenaireRepository $partenaireRepository, CompteRepository $compteRepository, RoleRepository $roleRepository, UserRepository $user,Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $values = json_decode($request->getContent());
        $userConnect = $this->tokenStorage->getToken()->getUser();
        $userPartenaire = $userConnect->getPartenaire();
        $res = $user->findBy(array("partenaire" => $userPartenaire));
        
        $compteRepository->findBy(array("partenaire" => $userPartenaire));
        
            $affectation = $serializer->deserialize($request->getContent(), Affectation::class, 'json');
            $d= $values->dateDebut;
            $f= $values->dateFin;
            $affectation->setDateDebut(\DateTime::createFromFormat('Y-m-d', $d))
                        ->setDateFin(\DateTime::createFromFormat('Y-m-d', $f))
                        ->setCompte($affectation->getCompte())
                        ->setUser($affectation->getUser());
            $manager->persist($affectation);
            
            $manager->flush();
            $data = [
                'status' => 201,
                'message' => 'Ok ... '
                ] ;
    
            return new JsonResponse($data, 201);
        
    }
}
