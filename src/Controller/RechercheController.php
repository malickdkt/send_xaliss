<?php

namespace App\Controller;

use App\Repository\CompteRepository;
use App\Repository\PartenaireRepository;
use App\Repository\AffectationRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RechercheController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /** 
     * @Route("/api/recherche/ninea", name="recherche_ninea", methods={"POST"})
     */
    public function rechercheNinea(Request $request, PartenaireRepository $partenaireRepository, SerializerInterface $serializer)
    {
        $values = json_decode($request->getContent());
        if ($values) {
            $partenaire = $partenaireRepository->findByNinea($values->ninea);
            if ($partenaire) {
                $data = $serializer->serialize($partenaire, 'json');

                return new Response($data, 200, [
                    'Content-Type' => 'application/json'
                ]);
            } else {
                $data = [
                    'status' => 500,
                    'message' => 'Le ninea n\'existe pas . '
                ];

                return new JsonResponse($data, 500);
            }
        } else {
            $data = [
                'status' => 500,
                'message' => 'Veuillez saisir le ninea . '
            ];

            return new JsonResponse($data, 500);
        }
    }

    /** 
     * @Route("/api/recherche/code", name="recherche_code", methods={"POST"})
     */
    public function rechercheCode(Request $request, TransactionRepository $transactionRepository, SerializerInterface $serializer)
    {
        $values = json_decode($request->getContent());
        if ($values) {
            $transaction = $transactionRepository->findByCode($values->code);
            if ($transaction) {
                $data = $serializer->serialize($transaction, 'json');

                return new Response($data, 200, [
                    'Content-Type' => 'application/json'
                ]);
            } else {
                $data = [
                    'status' => 500,
                    'message' => 'Le code n\'existe pas . '
                ];

                return new JsonResponse($data, 500);
            }
        } else {
            $data = [
                'status' => 500,
                'message' => 'Veuillez saisir le code . '
            ];

            return new JsonResponse($data, 500);
        }
    }


    /** 
     * @Route("/api/recherche/numero", name="recherche_numero", methods={"POST"})
     */
    public function rechercheNumeroCompte(Request $request, CompteRepository $compteRepository, SerializerInterface $serializer)
    {
        $values = json_decode($request->getContent());
        if ($values) {
            $compte = $compteRepository->findByNumCompte($values->numCompte);
            if ($compte) {
                $data = $serializer->serialize($compte, 'json');

                return new Response($data, 200, [
                    'Content-Type' => 'application/json'
                ]);
            } else {
                $data = [
                    'status' => 500,
                    'message' => 'Le numéro de compte n\'existe pas . '
                ];

                return new JsonResponse($data, 500);
            }
        } else {
            $data = [
                'status' => 500,
                'message' => 'Veuillez saisir le numéro de compte . '
            ];

            return new JsonResponse($data, 500);
        }
    }
    /**
     * Méthode qui affiche les infos du compte
     */
    public function getInfosCompte(AffectationRepository $affectationRepository, CompteRepository $compteRipo, SerializerInterface $serializer)
    {
        $userEnvoi = $this->tokenStorage->getToken()->getUser();
        $userAffcete = $affectationRepository->findOneBy(array("user" => $userEnvoi));
        #### Vérifie si l'utlisateur est affecté à un commpte ####
        if ($userAffcete) {
            $compte = $userAffcete->getCompte();
            $compte = $compteRipo->findOneBy(array("id" => $compte));
            $data[] = [
                'numCompte' => $compte->getNumCompte(),
                'solde' => $compte->getSolde()
            ];
            $result = $serializer->serialize($data, 'json');

            return new Response($result, 200, [
                'Content-Type' => 'application/json'
            ]);
        } else {
            return new Response('');
        }
    }
}
