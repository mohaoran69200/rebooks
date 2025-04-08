<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\ProfileFormType;
use App\Service\UserProfileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[IsGranted('ROLE_USER')]
#[Route('/profil', name: 'app_profile_')]
class UserProfileController extends AbstractController
{
    #[Route('/', name: 'view')]
    public function viewProfile(UserProfileService $profileService): Response
    {
        $user = $this->getUser();
        $books = $profileService->getUserBooks($user);

        return $this->render('profile/view.html.twig', [
            'user' => $user,
            'books' => $books
        ]);
    }

    #[Route('/modifier', name: 'edit')]
    public function editProfile(Request $request, UserProfileService $profileService): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profileService->updateProfile($user);
            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('app_profile_view');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/modifier-mot-de-passe', name: 'change_password')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function changePassword(
        Request $request,
        UserProfileService $userService,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if ($userService->changePassword($user, $oldPassword, $newPassword)) {
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Votre mot de passe a été mis à jour.');
                return $this->redirectToRoute('home');
            } else {
                $this->addFlash('error', 'L\'ancien mot de passe est incorrect.');
            }
        }


        return $this->render('security/change_password.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }

    #[Route('/supprimer', name: 'delete', methods: ['POST'])]
    public function deleteAccount(UserProfileService $profileService, TokenStorageInterface $tokenStorage, SessionInterface $session): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('home');
        }

        // Déconnexion de l'utilisateur
        $tokenStorage->setToken(null);
        $session->invalidate();

        // Supprimer le compte
        $profileService->deleteAccount($user);

        // Ajouter un message flash
        $this->addFlash('success', 'Compte supprimé avec succès.');

        // Redirection après suppression
        return $this->redirectToRoute('home');
    }
}