<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrationController extends AbstractController
{

    #[Route("/register", name: "app_register")]

    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordhashed)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encoder le mot de passe
            $user->setPassword($passwordhashed->hashPassword($user, $user->getPassword()));

            // Définir un rôle par défaut (par exemple, ROLE_USER)
            $user->setRoles(['ROLE_USER']);

            // Sauvegarder l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger l'utilisateur vers une page de confirmation ou la page d'accueil
            return $this->redirectToRoute('app_redirect_after_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'hide_roles' => true
        ]);
    }
}
