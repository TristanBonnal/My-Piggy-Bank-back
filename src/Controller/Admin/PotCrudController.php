<?php

namespace App\Controller\Admin;

use App\Entity\Pot;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
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
            TextField::new('name'),
            DateField::new('dateGoal'),
            MoneyField::new('amountGoal')->setCurrency('EUR'),
            DateField::new('createdAt'),
            DateField::new('updatedAt')->hideOnForm(),
            AssociationField::new('user')->autocomplete(),
            AssociationField::new('operations')->autocomplete(),
        ];
    }
    
}
