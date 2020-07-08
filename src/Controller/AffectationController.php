<?php

namespace App\Controller;

use App\Entity\Affectation;
use App\Repository\UserRepository;
use App\Repository\CompteRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AffectationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AffectationController extends AbstractController
{

    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/api/affectation", name="affectation", methods={"POST"})
     */
    public function affectation(AffectationRepository $affect,  CompteRepository $compteRepository, UserRepository $user, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $values = json_decode($request->getContent());
        $userConnect = $this->tokenStorage->getToken()->getUser();

        $affectation = $serializer->deserialize($request->getContent(), Affectation::class, 'json');
        $dateJour  = new \DateTime();

        $dateDebut = strtotime($values->dateDebut);
        $dateFin = strtotime($values->dateFin);
        $dateJour = strtotime($dateJour->format('Y-m-d'));
        ##### Vérifie si la date est passée ######

        if ($dateDebut < $dateJour) {
            $data = [
                'status' => 500,
                'message' => 'Impossible d\'affecter à une date pasée . '
            ];
            return new JsonResponse($data, 500);
        }

        ##### Vérifie si la date de fin supérieur à la date de début ######
        if ($dateFin < $dateDebut) {
            $data = [
                'status' => 500,
                'message' => 'La date de fin doit être doit supérieur à la date de début . '
            ];
            return new JsonResponse($data, 500);
        }
        $result = $affect->findAll();
        if ($result) {

            foreach ($result as $val) {
                $ddf = date_format($val->getDateDebut(), 'Y-m-d');
                $dff = date_format($val->getDateFin(), 'Y-m-d');
                if ($values->dateDebut <= $dff && $values->dateFin >= $dff && ($val->getCompte() === $affectation->getCompte() ||  $val->getUser() === $affectation->getUser())) {
                    $data = [
                        'status' => 500,
                        'message' => 'Un compte a été affecté cet utilisareur du (' . $ddf . ' - ' . $dff . ')'
                    ];

                    return new JsonResponse($data, 500);
                }
            }
        }

        ##### Vérifie si l'utilisateur a le role USER_PARTENAIRE ######
        if ($affectation->getUser()->getRole()->getLibelle() === "ROLE_CAISSIER_PARTENAIRE") {

            $affectation->setDateDebut(\DateTime::createFromFormat('Y-m-d', $values->dateDebut))
                ->setDateFin(\DateTime::createFromFormat('Y-m-d', $values->dateFin))
                ->setCompte($affectation->getCompte())
                ->setUser($affectation->getUser());
            $manager->persist($affectation);

            $manager->flush();
            $data = [
                'status' => 201,
                'message' => 'L\'affectation a réussi avec sucess... '
            ];

            return new JsonResponse($data, 201);
        } else {
            $data = [
                'status' => 500,
                'message' => 'L\'affectation n\'est autorisé qu\'aux users partenaire... '
            ];

            return new JsonResponse($data, 500);
        }
    }
}
