<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServiceController extends AbstractController
{
    #[Route('/service/{name}', name: 'app_service')]
    public function showService(string $name): Response
    {
        return $this->render('service/showService.html.twig', [
            'name' => $name,
        ]);
    }

    #[Route('/index', name: 'app_service_index')]
    public function gotoIndex(): Response //redirection vers la page d'accueil
    {
        return $this->redirectToRoute('app_home');
    }
}
