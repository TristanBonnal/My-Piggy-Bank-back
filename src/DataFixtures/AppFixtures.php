<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct (UserPasswordHasherInterface $hasher, Connection $connexion)
    {
        $this->hasher = $hasher;
        $this->connexion = $connexion;
    }

    public function truncate ()
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE user');
    }

    public function load(ObjectManager $manager): void
    {

        $this->truncate();

        $faker = Factory::create('fr_FR');

        // Admin
        $newAdmin = new User;
        $newAdmin
            ->setEmail($faker->email())
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($newAdmin, 'admin'))
            ->setUsername($faker->username())
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setBirthDate($faker->dateTimeBetween('-100 years', '-18 years'))
            ->setAddress($faker->streetaddress())
            ->setCity($faker->city())
            ->setZipCode((int)$faker->postcode())
            ->setCountry("France")
            ->setPhone($faker->phoneNumber())
            ->setIban($faker->iban())
            ->setBic($faker->swiftBicNumber())
            ->setCreatedAt($faker->dateTimeBetween('-2 years', 'now'));
        ;
        $manager->persist($newAdmin);
        

        for ($i = 1; $i <= 4; $i++) {

            $newUser = new User();
            $newUser->setEmail($faker->email());
            $newUser->setFirstname($faker->firstName());
            $newUser->setPassword($this->hasher->hashPassword($newUser, 'user'));
            $newUser->setLastname($faker->lastName());
            $newUser->setBirthDate($faker->dateTimeBetween('-100 years', '-18 years'));
            $newUser->setUsername($faker->username());
            $newUser->setAddress($faker->streetaddress());
            $newUser->setCity($faker->city());
            $newUser->setZipCode((int)$faker->postcode());
            $newUser->setCountry("France");
            $newUser->setPhone($faker->phoneNumber());
            $newUser->setIban($faker->iban());
            $newUser->setBic($faker->swiftBicNumber());
            $newUser->setCreatedAt($faker->dateTimeBetween('-2 years', 'now'));

            $manager->persist($newUser);

        }

        $manager->flush();
    }
}
