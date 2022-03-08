0 = Souple
1 = Mixte
2 = Strict

## Mode Souple
Retrait libre, dépots libre

## Mode Strict
1. Si date définie et si date du jour est inférieure à la date définie => Impossible de retirer
2. Si montant définie et montant actuel de la cagnotte est inférieur au montant définit => Impossible de retirer
3. Si date ET montant définis, retrait possible si les deux objectifs sont atteints

## Mode Mixte
1. Si date définie et si date du jour est inférieure à la date définie => Un retrait max de 50 % de la cagnotte
2. idem montant
3. idem montant + date


<?php
$pot = $operation->getPot();
if (!$operation->getType()) {   // si retrait
    
    if ($pot->getType() == 2) { // si mode strict
        // si objectif date défini et aujourd'hui < date ou si le montant de la cagnotte  est défini et la somme de la cagnotte < objectif montant fixé
        if ($pot->getDateGoal() && date("now") < $pot->getDateGoal() ||
            $pot->getAmountGoal() && $calculator->calculateAmount($pot) < $pot->getAmountGoal()) { 
                throw new Exception('Vous ne pouvez pas retirer car vous êtes en mode strict'); // On envoie une erreur
        }
    } 
    
    if ($pot->getType() == 1) { // si mode mixte
        // si objectif date ou montant défini et l'un des deux non atteint
        if ($pot->getDateGoal() && date("now") < $pot->getDateGoal() ||
            $pot->getAmountGoal() && $calculator->calculateAmount($pot) < $pot->getAmountGoal()) { 
                // Si pas retrait deja effectué ou si le montant du retrait voulu dépasse 50% de la cagnotte
               if ($cashouts >= 1 || $operation->getAmount() > $calculator->calculateAmount($pot) / 2)  { 
                   throw new Exception ('Vous ne pouvez retirer que la moitié de la somme, et ce une seule fois par cagnotte mixte !'); // On envoie une erreur
               }       
        }
    }
}

