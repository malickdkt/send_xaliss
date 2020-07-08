<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ListeTransactionController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("api/list/envois", name="liste_envoi",  methods={"GET"})
     */
    public function listeEnvoi(TransactionRepository $transactionRepository)
    {
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();
        $userPartenaire_id = $userConnecte->getId();
        $partenaire_id = $userConnecte->getPartenaire();
        if($roleUser === "ROLE_ADMIN_PARTENAIRE" || $roleUser === "ROLE_PARTENAIRE" )
        {
            if($partenaire_id)
            {
                $data = $transactionRepository->findByEnvois($partenaire_id);
            }
             
        }
        elseif($roleUser === "ROLE_USER_PARTENAIRE")
        {
            $data = $transactionRepository->findByEnvoisUserPartenaire($userPartenaire_id);
        }
        else
        {
            $data = '';
        }
        return new JsonResponse($data, 200);
    }

    /**
     * @Route("api/list/retraits", name="liste_retrait",  methods={"GET"})
     */
    public function listeRetrait(TransactionRepository $transactionRepository)
    {
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();
        $userPartenaire_id = $userConnecte->getId();
        $partenaire_id = $userConnecte->getPartenaire();
        if($roleUser === "ROLE_ADMIN_PARTENAIRE" || $roleUser === "ROLE_PARTENAIRE" )
        {
            if($partenaire_id)
            {
                $data = $transactionRepository->findByRetraits($partenaire_id);
            }
             
        }
        elseif($roleUser === "ROLE_USER_PARTENAIRE")
        {
            $data = $transactionRepository->findByRetraitsUserParteanaire($userPartenaire_id);
        }
        else
        {
            $data = '';
        }
        return new JsonResponse($data, 200);
    }
}
