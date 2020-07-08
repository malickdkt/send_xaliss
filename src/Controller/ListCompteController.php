<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\CompteRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Annotation\Groups;


class ListCompteController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/api/list/comptes", name="list_compte", methods={"GET"})
      
     */
    public function showCompte(CompteRepository $compte, SerializerInterface $serializer)
    {
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();
        $partenaire_id = $userConnecte->getPartenaire();
        
        if($roleUser === "ROLE_PARTENAIRE" || $roleUser === "ROLE_ADMIN_PARTENAIRE" )
        {
            if($partenaire_id)
            {
                $res = $compte->findBy(array("partenaire" => $partenaire_id));
            }
          
        }
        elseif ($roleUser === "ROLE_SUPER_ADMIN" || $roleUser === "ROLE_ADMIN" )
        {
            $res = $compte->findAll();
        }
        else
        {
            //$data =  'Votre role de vous permet aps de lister des comptes';
                
            return new Response();
        }
        $data = $serializer->serialize($res, 'json');
        
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);

    }
}
