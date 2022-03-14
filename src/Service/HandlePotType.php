<?php
namespace App\Service;

use App\Entity\Pot;
use App\Entity\Operation;
use App\Repository\OperationRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class HandlePotType
{
    private $operationRepository;
    private $calculator;

    public function __construct (OperationRepository $operationRepository, TotalCalculator $calculator)
    {
        $this->operationRepository = $operationRepository;
        $this->calculator = $calculator;
    }

    public function checkType (Operation $operation)
    {
        $pot = $operation->getPot();
        $cashouts = $this->operationRepository->getCashoutsNumber($pot)[0][1];
        

        // Si retrait
        if (!$operation->getType()) {   
            
            // Si mode strict
            if ($pot->getType() === 2) { 
                // Si objectif date défini et aujourd'hui < date ou si le montant de la cagnotte  est défini et la somme de la cagnotte < objectif montant fixé
                if ($pot->getDateGoal() && new \Datetime() < $pot->getDateGoal() || $pot->getAmountGoal() && $this->calculator->calculateAmount($pot) < $pot->getAmountGoal()) { 
                        throw new Exception("Retrait impossible en mode strict tant que les objectifs n'ont pas été atteints", Response::HTTP_BAD_REQUEST); 
                }
            } 
            
            // Si mode mixte
            if ($pot->getType() === 1) { 
                // Si objectif date ou montant défini et l'un des deux non atteint
                if ($pot->getDateGoal() && new \Datetime() < $pot->getDateGoal() || $pot->getAmountGoal() && $this->calculator->calculateAmount($pot) < $pot->getAmountGoal()) {
                    // Si pas retrait deja effectué ou si le montant du retrait voulu dépasse 50% de la cagnotte
                    if ($cashouts >= 1 || $operation->getAmount() > ($this->calculator->calculateAmount($pot) / 2))  { 
                        throw new Exception ('Non autorisé : Seul un retrait de maximum 50% est possible en mode mixte', Response::HTTP_BAD_REQUEST); 
                    }       
                }
            }
        }
    }
}