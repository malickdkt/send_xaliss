<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddUserPartenaireController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage, UserPasswordEncoderInterface $encoder)
    {
        $this->tokenStorage = $tokenStorage;
        $this->encoder = $encoder;
    }
    /**
     * @Route("/api/user/partenaire", name="add_user_partenaire", methods={"POST"})
     */
    public function addUser(Request $request, EntityManagerInterface $manager, RoleRepository $roleRepo, UserRepository $userRepository)
    {
        $values = json_decode($request->getContent());
        $userConnect = $this->tokenStorage->getToken()->getUser();
        $partenaire_id =  $userConnect->getPartenaire();
        if ($partenaire_id !== null) {
            $role = $roleRepo->findOneBy(array('libelle' => $values->role));
            $mail = $userRepository->findOneBy(array("email" => $values->email));
            if ($mail !== null) {
                $data = [
                    'status' => 500,
                    'message' => 'Cet adresse email existe déja . '
                ];

                return new JsonResponse($data, 500);
            }
            $user = new User;
            $user->setEmail($values->email)
                ->setRole($role)
                ->setPassword($this->encoder->encodePassword($user, $values->password))
                ->setPrenom($values->prenom)
                ->setNom($values->nom)
                ->setPartenaire($partenaire_id);
            $manager->persist($user);
            $manager->flush();
            $data = [
                'status' => 201,
                'message' => 'L\'utilisateur partenaire a été bien ajouté . '
            ];
            return new JsonResponse($data, 201);
        } else {
            $data = [
                'status' => 500,
                'message' => 'Votre profil de ne vous permet d\'ajouter un user partenaire . '
            ];
            return new JsonResponse($data, 500);
        }
    }

    /**
     * @Route("/api/list/user/partenaire", name="list_user_partenaire", methods={"GET"})
     */
    public function listUserPartenaire(SerializerInterface $serializer, UserRepository $user)
    {
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();

        if ($roleUser  === "ROLE_PARTENAIRE") {
            $partenaire_id = $userConnecte->getPartenaire()->getId();
            $liste = $user->findUsersByPartenaire($partenaire_id);
        } elseif ($roleUser  === "ROLE_ADMIN_PARTENAIRE") {
            $partenaire_id = $userConnecte->getPartenaire()->getId();
            $liste = $user->findUsersByAdminPartenaire($partenaire_id);
        } else {
            return new Response('Votre role de vous permet aps de lister des ulisateurs', 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $data = $serializer->serialize($liste, 'json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/api/list/user/partenaire/affectation", name="list_user_partenaire_affectation", methods={"GET"})
     */
    public function listUserByaffectation(SerializerInterface $serializer, UserRepository $user)
    {
        $userConnecte = $this->tokenStorage->getToken()->getUser();
        $roleUser = $userConnecte->getRole()->getLibelle();

        if ($roleUser  === "ROLE_PARTENAIRE" || $roleUser  === "ROLE_ADMIN_PARTENAIRE") {
            $partenaire_id = $userConnecte->getPartenaire()->getId();
            $liste = $user->findUsersByAdminPartenaire($partenaire_id);
        } else {
            return new Response('Votre role de vous permet aps de lister des ulisateurs', 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $data = $serializer->serialize($liste, 'json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
