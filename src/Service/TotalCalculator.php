<?php
namespace App\Service;

use App\Entity\Pot;
use App\Repository\OperationRepository;

class TotalCalculator
{
    public function __construct(OperationRepository $operationRepository)
    {
        $this->operationRepository = $operationRepository;
    }

    /**
     * Récupère les opérations liées à une cagnotte et renvoie leur total
     *
     * @param Pot $pot
     * @return void
     */
    public function calculateAmount(Pot $pot)
    {

        $operations = $this->operationRepository->getOperationsByPot($pot);
        $total = 0;
        foreach ($operations as $operation) {
            $total += $operation->getType() ? $operation->getAmount() : -$operation->getAmount();
        }
        $pot->setAmount($total);
        return $pot->getAmount();
    }

    /**
     * Calcule le total des montants d'un tableau d'opérations, idéal pour les fixtures
     * 
     * @param array $operations non persistées
     * @return float
     */
    public function calculateOperations(array $operations):float
    {
        $total = 0;
        foreach ($operations as $operation) {
            $total += $operation->getType() ? $operation->getAmount() : -$operation->getAmount();
        }
        return $total;
    }
}