<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * On peut préfixer toutes les routes de ce contrôleur
 * 
 * @Route("/back/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="back_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('back/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="back_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Pour hacher le mot de passe :
            // - on a récupéré le service adéquat (injection dans la méthode du contrôleur)
            // - on lui donne sur la méthode hashPassword, notre $user
            // - et le mot de passe en clair (qui est déjà dnas le user !)
            $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
            // On écrase le mot de passe en clair par le mot de passe haché
            $user->setPassword($hashedPassword);

            // dd($user);
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur ajouté(e).');

            return $this->redirectToRoute('back_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="back_user_show", methods={"GET"})
     * 
     * $user = null permet de récupérer la main sur la 404
     */
    public function show(User $user = null): Response
    {
        if ($user === null) {
            throw $this->createNotFoundException('Utilisateur non trouvé(e).');
        }

        return $this->render('back/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="back_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gestion du mot de passe modifié ou non ?
            // On va le chercher directement dans le formulaire car non mappé sur l'entité
            if ($form->get('password')->getData()) {
                // Si oui, on hache le nouveau mot de passe
                $hashedPassword = $userPasswordHasher->hashPassword($user, $form->get('password')->getData());
                // On écrase le mot de passe en clair par le mot de passe haché
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié(e).');

            return $this->redirectToRoute('back_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="back_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        $this->addFlash('success', $user->getEmail() . ', supprimé.');

        return $this->redirectToRoute('back_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
