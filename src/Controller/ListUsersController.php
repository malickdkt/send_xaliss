<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ListUsersController extends AbstractController
{
    
   	private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/api/list/users", name="list_users", methods={"GET"})
     */

    
    public function showUser(SerializerInterface $serializer,UserRepository $user)
    {
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();
        
        
        if($roleUser  === "ROLE_SUPER_ADMIN")
        {
            $liste = $user->findUsersBySupAdmin();
        }
        elseif($roleUser  === "ROLE_ADMIN")
        {
            $liste = $user->findUsersByAdmin();
        }
        else
        {
            return new Response('Votre role de vous permet pas de lister des ulisateurs', 200, [
                'Content-Type' => 'application/json'
            ]);
           
        }
        $data = $serializer->serialize($liste, 'json');
        
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);

    	
    }

}
