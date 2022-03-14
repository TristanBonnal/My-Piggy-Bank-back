<?php

namespace App\Provider;

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

    public function getPotName()
    {
        return $this->name[array_rand($this->name)];
    }
}