<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\DepotRepository;



class ListDepotController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/api/list/depots", name="list_depots", methods={"GET"})
     */
    public function showDepot(DepotRepository $depot, SerializerInterface $serializer)
    {
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();


        if ($roleUser === "ROLE_PARTENAIRE" || $roleUser === "ROLE_ADMIN_PARTENAIRE") {
            $partenaire_id = $userConnecte->getPartenaire()->getId();
            if ($partenaire_id) {
                $res = $depot->findDepotByPartenaire($partenaire_id);
            }
        } elseif ($roleUser === "ROLE_SUPER_ADMIN" || $roleUser === "ROLE_ADMIN") {
            $res = $depot->findAll();
        } else {
            $data =  'Votre role de vous permet aps de lister des comptes';

            return new Response($data, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $data = $serializer->serialize($res, 'json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
