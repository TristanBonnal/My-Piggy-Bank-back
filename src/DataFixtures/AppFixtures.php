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

    /**
     * Permet de reset les id au chargement de nouvelles fixtures
     */
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
        // Bundle externe permettant la génération de données aléatoires
        $faker = Factory::create('fr_FR');

        // Création d'un admin
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
        // Création de cagnottes
        $rndIndex =  mt_rand(2,4);
        for ($i = 0; $i < $rndIndex; $i++) {
            $numberOrNull = [null, $faker->numberBetween(5000, 10000), $faker->numberBetween(5000, 10000)];
            $dateOrNull = [null, $faker->dateTimeBetween('now', '+2 years')];
            $newPotAdmin = new Pot();
            $newPotAdmin
                ->setName($faker->word(1, true))
                ->setDateGoal($dateOrNull[array_rand($dateOrNull)])
                ->setAmountGoal($numberOrNull[array_rand($numberOrNull)])
                // Type défini à souple si aucun objectif
                ->setType(empty($newPotAdmin->getAmountGoal()) && empty($newPotAdmin->getDateGoal()) ? 0 : mt_rand(0,2))
            ;

            $manager->persist($newPotAdmin);
            $newAdmin->addPot($newPotAdmin);

            // Opérations pour chaque cagnotte admin
            $adminOperations = [];
            $rndJndex =  mt_rand(3,8);
            for($j = 0; $j < $rndJndex; $j++) {

                $newOperationAdmin = new Operation();
                $newOperationAdmin->setType(mt_rand(0,1));
                $newOperationAdmin->setAmount($faker->numberBetween(100,1000));

                //Vérification du solde et du type de la cagnotte en cas de retrait, 
                if (!$newOperationAdmin->getType()) {
                    // Si le montant du retrait > somme totale de la cagnotte ou le mode est strict ou mixte
                    if  ($newOperationAdmin->getAmount() > $this->calculator->calculateOperations($adminOperations) ||
                        $newPotAdmin->getType() == 2 || $newPotAdmin->getType() == 1) {
                        continue;
                    }
                }
                $newAdmin->addOperation($newOperationAdmin);
                $newPotAdmin->addOperation($newOperationAdmin);
                $manager->persist($newOperationAdmin);
                $adminOperations[] = $newOperationAdmin;

            }
        }
        
        $manager->persist($newAdmin);


        //  Création users
        for ($i = 1; $i <= 5; $i++) {
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
            
            // Cagnottes users
            $rndJndex = mt_rand(0,6);
            for ($j = 0; $j < $rndJndex; $j++) {
                $numberOrNull = [null, $faker->numberBetween(5000, 10000)];
                $dateOrNull = [null, $faker->dateTimeBetween('now', '+2 years')];
                $newPotUser = new Pot();
                $newPotUser
                    ->setName($faker->word(1, true))
                    ->setDateGoal($dateOrNull[array_rand($dateOrNull)])
                    ->setAmountGoal($numberOrNull[array_rand($numberOrNull)])
                    // Type défini à souple si aucun objectif
                    ->setType(empty($newPotUser->getAmountGoal()) && empty($newPotUser->getDateGoal()) ? 0 : mt_rand(0,2))                    
                ;

                $manager->persist($newPotUser);
                $newUser->addPot($newPotUser);

                // Opérations User
                $userOperations = [];
                $rndK =  mt_rand(2,6);
                for ($k = 1; $k <  $rndK; $k++) {
                    $newOperation = new Operation();
                    $newOperation->setType(mt_rand(0,1));
                    $newOperation->setAmount($faker->numberBetween(100,1000));

                    //Vérification du solde de la cagnotte en cas de retrait
                    if (!$newOperation->getType()) {
                        // Si le montant du retrait > somme totale de la cagnotte ou le mode est strict ou mixte
                        if  ($newOperation->getAmount() > $this->calculator->calculateOperations($userOperations) ||
                            $newPotUser->getType() == 2 || $newPotUser->getType() == 1) {
                            continue;
                        }
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
