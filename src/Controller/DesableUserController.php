<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DesableUserController extends AbstractController
{
    /**
      * @Route("/api/users/status/{id}", name="status", methods={"GET"} )
      */
     public function status($id, UserRepository $userRepository, EntityManagerInterface $manager)
     {
        
         $user = $userRepository->find($id);
         $status = '';
         if ($user->getIsActive()=== true)
         {
             $status = 'bloquer';
             $user->setIsActive(false);
         }
         else
         {
             $status = 'débloquer';
             $user->setIsActive(true);
         }
         $manager->persist($user);
         $manager->flush();
         $data=[
             'status'=>200,
             'message'=> $user->getPrenom().' - '.$user->getNom().' est '. $status
         ];
         return $this->json($data, 200);
     }
 }
 