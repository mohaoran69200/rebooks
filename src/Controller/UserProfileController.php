<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Form\ChangePasswordType;
use App\Service\UserProfileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
        $form = $this->createForm(ProfileType::class, $user);
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
    public function changePassword(Request $request, UserProfileService $profileService): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profileService->changePassword($this->getUser(), $form->getData());
            $this->addFlash('success', 'Mot de passe modifié avec succès.');
            return $this->redirectToRoute('app_profile_view');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/supprimer', name: 'delete', methods: ['POST'])]
    public function deleteAccount(UserProfileService $profileService): Response
    {
        $profileService->deleteAccount($this->getUser());
        $this->addFlash('success', 'Compte supprimé avec succès.');
        return $this->redirectToRoute('app_home');
    }
}