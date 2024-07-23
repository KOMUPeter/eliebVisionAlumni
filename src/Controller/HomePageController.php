<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(): Response
    {
        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
        ]);
    }

    #[Route('/constitution', name: 'constitution')]
    public function constitution(): Response
    {
        return $this->render('constitution/index.html.twig', [
            'controller_name' => 'HomePageController',
        ]);
    }

    #[Route('/terms_and_condations', name: 'terms_and_condations')]
    public function termsAndCondations(): Response
    {
        return $this->render('termsAndCondations/index.html.twig', [
            'controller_name' => 'HomePageController',
        ]);
    }

    #[Route('/privacy', name: 'PrivacyPage')]
    public function privacy(): Response
    {
        return $this->render('privacy/index.html.twig', [
            'controller_name' => 'HomePageController',
        ]);
    }
}
