<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Entity\Casting;
use App\Form\CastingType;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Repository\CastingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * On peut préfixer toutes les routes de ce contrôleur
 * 
 * @Route("/back/casting")
 */
class CastingController extends AbstractController
{
    /**
     * Browse castings for a given movie
     * 
     * @Route("/movie/{id}", name="back_casting_index", methods={"GET"})
     */
    public function index(Movie $movie, CastingRepository $castingRepository): Response
    {
        return $this->render('back/casting/index.html.twig', [
            'movie' => $movie,
            'castings' => $castingRepository->findAllJoinedToPersonByMovieQb($movie),
        ]);
    }

    /**
     * @Route("/new/movie/{id}", name="back_casting_new", methods={"GET", "POST"})
     */
    public function new(Movie $movie, Request $request, EntityManagerInterface $entityManager): Response
    {
        $casting = new Casting();
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On associe le film au casting
            $casting->setMovie($movie);

            $entityManager->persist($casting);
            $entityManager->flush();

            $this->addFlash('success', 'Casting ajouté(e).');

            // On retourne à la liste des castings du film
            return $this->redirectToRoute('back_casting_index', ['id' => $movie->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/casting/new.html.twig', [
            'form' => $form,
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{id}", name="back_casting_show", methods={"GET"})
     */
    // public function show(Movie $movie): Response
    // {
    //     return $this->render('back/casting/show.html.twig', [
    //         'movie' => $movie,
    //     ]);
    // }

    /**
     * @Route("/{id}/edit", name="back_casting_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Casting $casting, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Casting modifié(e).');

            return $this->redirectToRoute('back_casting_index', ['id' => $casting->getMovie()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/casting/edit.html.twig', [
            'form' => $form,
            'casting' => $casting,
        ]);
    }

    /**
     * @Route("/{id}", name="back_casting_delete", methods={"POST"})
     */
    public function delete(Request $request, Casting $casting, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'. $casting->getId(), $request->request->get('_token'))) {
            $entityManager->remove($casting);
            $entityManager->flush();

            $this->addFlash('success', 'Casting supprimé.');
        }

        return $this->redirectToRoute('back_casting_index', ['id' => $casting->getMovie()->getId()], Response::HTTP_SEE_OTHER);
    }
}
