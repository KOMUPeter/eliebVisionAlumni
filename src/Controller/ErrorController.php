<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error', name: 'error')]
    public function show(): Response
    {
        return $this->render('error/error.html.twig');
    }

    #[Route('/not-found', name: 'not_found')]
    public function notFound(): Response
    {
        return $this->render('error/error.html.twig', [
            'message' => 'The page you are looking for does not exist. Please check the URL or return to the home page.',
        ]);
    }
}
