<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_home')]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_USER', $user->getRoles())) {
                return $this->render('user/index.html.twig', [
                    'controller_name' => 'UserController',
                ]);
            }
            if (in_array('BANNED_USER', $user->getRoles())) {
                return $this->render('user/banneed_user_page.html.twig', [
                    'controller_name' => 'UserController',
                ]);
            }
        }

    }

    #[Route('/myProfile', name: 'myProfile')]
    public function myProfile(): Response
    {
        return $this->render('user/my_profil.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


}
