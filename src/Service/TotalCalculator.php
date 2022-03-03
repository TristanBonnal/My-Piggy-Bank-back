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
     * Récupère les opérations liée à une cagnotte et renvoie leur total
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
    }
}