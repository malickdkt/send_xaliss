<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $role_super_admin = new Role();
        $role_super_admin->setLibelle("ROLE_SUPER_ADMIN");
        $manager->persist($role_super_admin);

        $role_admin = new Role();
        $role_admin->setLibelle("ROLE_ADMIN");
        $manager->persist($role_admin);

        $role_caissier = new Role();
        $role_caissier->setLibelle("ROLE_CAISSIER");
        $manager->persist($role_caissier);
        
        $user = new User();
        $user->setEmail("cheikh3008@gmail.com")
            ->setRole($role_super_admin)
            ->setPassword($this->encoder->encodePassword( $user , "admin123"))
            ->setPrenom("Cheikh")
            ->setNom("Dieng")
            ->setIsActive(true);
        $manager->persist($user);
        $manager->flush();
    }
}
