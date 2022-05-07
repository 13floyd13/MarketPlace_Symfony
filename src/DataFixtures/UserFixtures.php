<?php

namespace App\DataFixtures;

use App\Entity\Usager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    private ?UserPasswordHasherInterface $passwordHasher;

    //constructeur pour appeler le password hasher
    public function __construct(UserPasswordHasherInterface $passwordHasher){
        $this->passwordHasher = $passwordHasher;
    }

    //creation d'un admin
    public function load(ObjectManager $manager)
    {
        $usagerAdmin = new Usager();
        $usagerAdmin->setPassword("admin");
        $hashedPassword = $this->passwordHasher->hashPassword($usagerAdmin, $usagerAdmin->getPassword());
        $usagerAdmin->setPassword($hashedPassword);
        $usagerAdmin->setRoles(["ROLE_ADMIN"]);
        $usagerAdmin->setNom("Admin");
        $usagerAdmin->setPrenom("Admin");
        $usagerAdmin->setEmail("admin@admin.com");
        $manager->persist($usagerAdmin);
        $manager->flush();

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::ADMIN_USER_REFERENCE, $usagerAdmin);
    }
}