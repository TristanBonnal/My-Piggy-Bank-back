<?php

namespace App\Controller\Admin;

use App\Entity\Operation;
use App\Entity\Pot;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * On retourne le bon template qui sera l'accueil du Dashboard
     * 
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('My Piggy Back');
    }

    // On configure ce qu'on veut de disponible dans le Dashboard afin de gérer le CRUD
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Cagnottes', 'fas fa-piggy-bank', Pot::class);
        yield MenuItem::linkToCrud('Opérations', 'fas fa-money-bill', Operation::class);
    }
}
