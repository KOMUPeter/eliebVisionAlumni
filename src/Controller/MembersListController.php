<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MembersListController extends AbstractController
{
    #[Route('/membersList', name: 'membersList')]
    #[IsGranted('ROLE_USER')]
    public function membersList(UsersRepository $usersRepository): Response
    {

        $membersLists = $usersRepository->findAll();
        return $this->render('members_list/index.html.twig', [
            'membersLists' => $membersLists,
        ]);
    }
}
