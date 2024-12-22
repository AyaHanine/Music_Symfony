<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $username = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', parameters: [
            '_username' => $username,
            'error' => $error
        ]);
    }



    #[Route('/logout', name: 'logout_page')]

    public function logout(): void
    {
    }


    #[Route("/reset-password", name: "reset_password")]

    public function resetPassword(Request $request)
    {
    }

    #[Route('/redirect', name: 'app_redirect_after_login')]
    public function redirectAfterLogin(TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;


        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }

        if (in_array('ROLE_USER', $user->getRoles())) {
            return $this->redirectToRoute('user_home');
        }

        if (in_array('BANNED_USER', $user->getRoles())) {
            return $this->redirectToRoute('user_home');
        }

        return $this->redirectToRoute('app_home');
    }
}
