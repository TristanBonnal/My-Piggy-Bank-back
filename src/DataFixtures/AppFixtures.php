<?php

namespace App\DataFixtures;

use App\Entity\Operation;
use App\Entity\User;
use App\Entity\Pot;
use App\Service\TotalCalculator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct (UserPasswordHasherInterface $hasher, Connection $connexion, TotalCalculator $calculator)
    {
        $this->hasher = $hasher;
        $this->connexion = $connexion;
        $this->calculator = $calculator;
    }

    public function truncate ()
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE user');
        $this->connexion->executeQuery('TRUNCATE TABLE pot');
        $this->connexion->executeQuery('TRUNCATE TABLE operation');
    }

    public function load(ObjectManager $manager): void
    {

        $this->truncate();

        $faker = Factory::create('fr_FR');

        // Admin
        $newAdmin = new User;
        $newAdmin
            ->setEmail('admin@admin.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($newAdmin, 'admin'))
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setBirthDate($faker->dateTimeBetween('-100 years', '-18 years'))
            ->setPhone($faker->phoneNumber())
            ->setIban($faker->iban())
            ->setBic($faker->swiftBicNumber())
            ->setCreatedAt($faker->dateTimeBetween('-2 years', 'now'))

        ;
        $rndIndex =  mt_rand(2,4);
        for ($i = 0; $i < $rndIndex; $i++) {
            $numberOrNull = [null, $faker->numberBetween(100, 10000)];
            $dateOrNull = [null, $faker->dateTimeBetween('now', '+2 years')];
            $newPotAdmin = new Pot();
            $newPotAdmin
                ->setName($faker->word(1, true))
                ->setDateGoal($dateOrNull[array_rand($dateOrNull)])
                ->setAmountGoal($numberOrNull[array_rand($numberOrNull)])
            ;
            $manager->persist($newPotAdmin);
            $newAdmin->addPot($newPotAdmin);

            // Opérations Admin
            $adminOperations = [];
            $rndJndex =  mt_rand(2,6);
            for($j = 0; $j < $rndJndex; $j++) {

                $newOperationAdmin = new Operation();
                $newOperationAdmin->setType(mt_rand(0,1));
                $newOperationAdmin->setAmount($faker->numberBetween(1,10000));

                //Vérification du solde de la cagnotte en cas de retrait
                if (!$newOperationAdmin->getType() && ($newOperationAdmin->getAmount() > $this->calculator->calculateOperations($adminOperations))) {
                    continue;
                }
                $newAdmin->addOperation($newOperationAdmin);
                $newPotAdmin->addOperation($newOperationAdmin);
                $manager->persist($newOperationAdmin);
                $adminOperations[] = $newOperationAdmin;

            }
        }
        
        $manager->persist($newAdmin);


        //  User
        for ($i = 1; $i <= 4; $i++) {
            $newUser = new User();
            $newUser->setEmail($faker->email());
            $newUser->setFirstname($faker->firstName());
            $newUser->setPassword($this->hasher->hashPassword($newUser, 'user'));
            $newUser->setLastname($faker->lastName());
            $newUser->setBirthDate($faker->dateTimeBetween('-100 years', '-18 years'));
            $newUser->setPhone($faker->phoneNumber());
            $newUser->setIban($faker->iban());
            $newUser->setBic($faker->swiftBicNumber());
            $newUser->setCreatedAt($faker->dateTimeBetween('-2 years', 'now'));

            $rndJndex = mt_rand(0,4);
            for ($j = 0; $j < $rndJndex; $j++) {
                $numberOrNull = [null, $faker->numberBetween(100, 10000)];
                $dateOrNull = [null, $faker->dateTimeBetween('now', '+2 years')];
                $newPotUser = new Pot();
                $newPotUser
                    ->setName($faker->word(1, true))
                    ->setDateGoal($dateOrNull[array_rand($dateOrNull)])
                    ->setAmountGoal($numberOrNull[array_rand($numberOrNull)])
                ;

                $manager->persist($newPotUser);
                $newUser->addPot($newPotUser);

                // Opérations User
                $userOperations = [];
                $rndK =  mt_rand(1,5);
                for ($k = 1; $k <  $rndK; $k++) {
                    $newOperation = new Operation();
                    $newOperation->setType(mt_rand(0,1));
                    $newOperation->setAmount($faker->numberBetween(1,10000));

                    //Vérification du solde de la cagnotte en cas de retrait
                    if (!$newOperation->getType() && ($newOperation->getAmount() > $this->calculator->calculateOperations($userOperations))) {
                        continue;
                    }
                    $newUser->addOperation($newOperation);
                    $newPotUser->addOperation($newOperation);
                    $manager->persist($newOperation);
                    $userOperations[] = $newOperation;
                }
            }
            $manager->persist($newUser);
        }


        $manager->flush();
    }
}
