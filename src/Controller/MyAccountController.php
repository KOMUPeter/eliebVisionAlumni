<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;

class MyAccountController extends AbstractController
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/myAccount', name: 'myAccount')]
    public function changeUserPassword(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to access this page.');
        }

        // here is to change users password
        // Create the form and handle request
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Validate current password
            if (!$this->userPasswordHasher->isPasswordValid($user, $data['currentPassword'])) {
                $form->get('currentPassword')->addError(new FormError('Current password is incorrect.'));
            } else {
                // Update password
                $newPassword = $this->userPasswordHasher->hashPassword($user, $data['newPassword']);
                $user->setPassword($newPassword);

                // Update user entity
                $this->entityManager->flush();

                $this->addFlash('success', 'Password changed successfully.');

                return $this->redirectToRoute('app_home_page');
            }
        }

        return $this->render('my_account/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
