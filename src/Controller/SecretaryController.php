<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecretaryController extends AbstractController
{
    #[Route('/secretary', name: 'app_secretary')]
    public function secretary(): Response
    {
        return $this->render('secretary/index.html.twig', [
            'controller_name' => 'SecretaryController',
        ]);
    }
}
