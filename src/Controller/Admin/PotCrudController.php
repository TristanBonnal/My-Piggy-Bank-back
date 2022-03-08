<?php

namespace App\Controller\Admin;

use App\Entity\Pot;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PotCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Pot::class;
    }

    // On configure les champs
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            DateField::new('dateGoal', 'Objectif de date'),
            MoneyField::new('amountGoal', 'Montant à atteindre')->setCurrency('EUR')->setStoredAsCents(false),
            DateField::new('createdAt', 'Créee le'),
            DateField::new('updatedAt', 'Modifiée le')->hideOnForm(),
            AssociationField::new('user', 'Utilisateur')->autocomplete(),
            AssociationField::new('operations', 'Opérations')->autocomplete(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Cagnotte')
            ->setEntityLabelInPlural('Cagnottes')
        ;
    }
    
}
