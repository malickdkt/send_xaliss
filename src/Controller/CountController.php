<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\DepotRepository;
use App\Repository\CompteRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CountController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/api/count", name="count", methods={"GET"})
     */
    public function compter(CompteRepository $compte, UserRepository $user, TransactionRepository $transactionRepository, DepotRepository $depotRepository)
    {
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();
        $partenaire_id = $userConnecte->getPartenaire();
        if ($roleUser === "ROLE_PARTENAIRE" || $roleUser === "ROLE_ADMIN_PARTENAIRE") {

            $data[] = [
                "compte" => count($compte->findBy(array("partenaire" => $partenaire_id))),
                "user" => count($user->findBy(array("partenaire" => $partenaire_id))) - 1,
                "envoi" => count($transactionRepository->findByEnvois($partenaire_id)),
                "retrait" => count($transactionRepository->findByRetraits($partenaire_id)),

            ];
        } elseif ($roleUser === "ROLE_SUPER_ADMIN" || $roleUser === "ROLE_ADMIN") {
            $data[] = [
                "partenaire" => count($user->findByPartenaire()),
                "user" => count($user->findUsersBySupAdmin()),
                "compte" => count($compte->findAll()),
                "depot" => count($depotRepository->findAll()),

            ];
        } else {
            $data[] = [];
        }
        return new JsonResponse($data, 200);
    }
    /**
     * @Route("/api/recherche/parts", name="part_systeme", methods={"POST"})
     */
    public function listerPartSysteme(TransactionRepository $transactionRepository, Request $request, SerializerInterface $serializer)
    {
        $values = json_decode($request->getContent());
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();
        $partenaire_id = $userConnecte->getPartenaire();
        $dd = (\DateTime::createFromFormat('Y-m-d', $values->dateDebut));
        $df = (\DateTime::createFromFormat('Y-m-d', $values->dateFin));
        if ($roleUser === "ROLE_SUPER_ADMIN" || $roleUser === "ROLE_ADMIN") {
            $data = $transactionRepository->recherchePartSysteme($dd, $df);
            $result = $serializer->serialize($data, 'json');
        } elseif ($roleUser === "ROLE_PARTENAIRE" || $roleUser === "ROLE_ADMIN_PARTENAIRE") {
            $data =  $transactionRepository->recherchePartPartenaire($partenaire_id, $dd, $df);
            $result = $serializer->serialize($data, 'json');
        } else {
            $result = [];
        }

        return new Response($result, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/api/liste/parts/envois", name="part_partenaire_envois", methods={"GET"})
     */
    public function listerPartPartenaireEnvois(TransactionRepository $transactionRepository)
    {
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();
        $partenaire_id = $userConnecte->getPartenaire();
        if ($roleUser === "ROLE_ADMIN_PARTENAIRE" || $roleUser === "ROLE_PARTENAIRE") {
            if ($partenaire_id) {
                $result = $transactionRepository->findByPartEnvoisPartenaire($partenaire_id);
            }
        } elseif ($roleUser === "ROLE_ADMIN" || $roleUser === "ROLE_SUPER_ADMIN") {
            $result = $transactionRepository->findByPartSysteme();
        } else {
            $data = [
                'status' => 500,
                'message' => 'Vous n\'êtes autorisés accéder à ce service. '
            ];

            return new JsonResponse($data, 500);
        }
        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/api/liste/parts/retraits", name="part_partenaire_retraits", methods={"GET"})
     */
    public function listerPartPartenaireRetraits(TransactionRepository $transactionRepository)
    {
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();
        $partenaire_id = $userConnecte->getPartenaire();
        if ($roleUser === "ROLE_ADMIN_PARTENAIRE" || $roleUser === "ROLE_PARTENAIRE") {
            if ($partenaire_id) {
                $result = $transactionRepository->findByPartRetraitsPartenaire($partenaire_id);
            }
        } elseif ($roleUser === "ROLE_ADMIN" || $roleUser === "ROLE_SUPER_ADMIN") {
            $result = $transactionRepository->findByPartSysteme();
        } else {
            $data = [
                'status' => 500,
                'message' => 'Vous n\'êtes autorisés accéder à ce service. '
            ];

            return new JsonResponse($data, 500);
        }
        return new JsonResponse($result, 200);
    }
}
