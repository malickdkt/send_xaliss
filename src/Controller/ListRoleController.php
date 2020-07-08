<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\RoleRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;



class ListRoleController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/api/list/roles", name="list_roles", methods={"GET"})
     */

    public function showRole(RoleRepository $rp, SerializerInterface $serializer)
    {

        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $role = $userConnecte->getRole()->getLibelle();

        if ($role === "ROLE_SUPER_ADMIN") {
            $liste = $rp->findBy(array("libelle" => ["ROLE_ADMIN", "ROLE_CAISSIER"]));
        } elseif ($role === "ROLE_ADMIN") {
            $liste = $rp->findBy(array("libelle" => ["ROLE_CAISSIER"]));
        } elseif ($role === "ROLE_PARTENAIRE") {
            $liste = $rp->findBy(array("libelle" => ["ROLE_ADMIN_PARTENAIRE", "ROLE_CAISSIER_PARTENAIRE"]));
        } elseif ($role === "ROLE_ADMIN_PARTENAIRE") {
            $liste = $rp->findBy(array("libelle" => ["ROLE_CAISSIER_PARTENAIRE"]));
        } else {
            return new Response('Votre role de vous permet aps de lister des roles', 200, [
                'Content-Type' => 'application/json'
            ]);
        }
        $data = $serializer->serialize($liste, 'json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
