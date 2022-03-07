<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    // On configure les champs
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email'),
            ArrayField::new('roles', 'Rôle'),
            TextField::new('firstname', 'Prénom'),
            TextField::new('lastname', 'Nom'),
            DateField::new('birthDate', 'Date de naissance'),
            BooleanField::new('status', 'Statut'),
            TextField::new('phone', 'Numéro de téléphone'),
            DateField::new('createdAt', 'Créé le'),
            DateField::new('updatedAt', 'Modifié le')->hideOnForm(),
            TextField::new('iban'),
            TextField::new('bic'),
            AssociationField::new('pots', 'Cagnottes')->autocomplete(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
        ;
    }
    
}
