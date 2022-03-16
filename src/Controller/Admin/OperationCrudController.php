<?php

namespace App\Controller\Admin;

use App\Entity\Operation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class OperationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Operation::class;
    }

    // On configure les champs
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ChoiceField::new('type', 'Type')->setChoices([
                "Retrait" => 0,
                "Dépot" => 1,
            ]),
            MoneyField::new('amount', 'Montant')->setCurrency('EUR')->setStoredAsCents(false),
            DateField::new('createdAt', 'Créée le'),
            AssociationField::new('pot', 'Cagnotte'),
            AssociationField::new('user', 'Utilisateur'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Opération')
            ->setEntityLabelInPlural('Opérations')
        ;
    }

    public function configureActions(Actions $actions): Actions 
    {
        return $actions
            // ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ;
    }
    
}
