<?php

namespace App\AppFixtures\Provider;

// Provider to be used in the fixtures

class PotNameProvider
{
    private $name = [
        "Voyage",
        "Mariage",
        "Baptême",
        "Voiture",
        "Appartement",
        "Cadeaux",
        "Anniversaire",
        "Noël"
    ];

    // Get one random pot's name

    public function getPotName()
    {
        return $this->name[array_rand($this->name)];
    }
}