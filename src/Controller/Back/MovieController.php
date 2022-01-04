<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Service\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * On peut préfixer toutes les routes de ce contrôleur
 * 
 * @Route("/back/movie")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/", name="back_movie_index", methods={"GET"})
     */
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('back/movie/index.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="back_movie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, MySlugger $mySlugger): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On slugifie le titre
            $movie->setSlug($mySlugger->slugify($movie->getTitle()));

            $entityManager->persist($movie);
            $entityManager->flush();

            $this->addFlash('success', 'Film ou série ajouté(e).');

            return $this->redirectToRoute('back_movie_show', ['id' => $movie->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="back_movie_show", methods={"GET"})
     * 
     * $movie = null permet de révupérer la main sur la 404
     */
    public function show(Movie $movie = null): Response
    {
        if ($movie === null) {
            throw $this->createNotFoundException('Film ou série non trouvé(e).');
        }

        return $this->render('back/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="back_movie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Movie $movie, EntityManagerInterface $entityManager, MySlugger $mySlugger): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On slugifie le titre
            // /!\ SEO : cela va casser le référencement actuel...
            // idéalement on utilisera une URL du type /{id}/{slug} comme sur le blog O'clock
            $movie->setSlug($mySlugger->slugify($movie->getTitle()));

            $entityManager->flush();

            $this->addFlash('success', 'Film ou série modifié(e).');

            return $this->redirectToRoute('back_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="back_movie_delete", methods={"POST"})
     */
    public function delete(Request $request, Movie $movie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($movie);
            $entityManager->flush();
        }

        $this->addFlash('success', $movie->getTitle() . ', supprimé.');

        return $this->redirectToRoute('back_movie_index', [], Response::HTTP_SEE_OTHER);
    }
}
