<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Model\Movies;
use App\Repository\MovieRepository;
use App\Repository\CastingRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Un use est nécessaire pour les @Route
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function home(MovieRepository $movieRepository)
    {
        // On va chercher les données
        $moviesList = $movieRepository->findLatestByReleaseDateDql();

        // On rend un template Twig à partir du dossier templates/
        // en lui donnant les données nécessaires
        return $this->render('front/main/home.html.twig', [
            // Variable Twig => variable PHP
            'moviesList' => $moviesList,
        ]);
    }

    /**
     * Display movie/serie
     * 
     * @param string $slug Slug of the movie to display
     * 
     * @Route("/movie/{slug}", name="main_movie_show")
     */
    public function movieShow(Movie $movie = null, MovieRepository $movieRepository, CastingRepository $castingRepository)
    {
        // /!\ On a récupéré $movie via le ParamConverter depuis l'URL

        // On vérifie ce qu'on a reçu
        // dump($movie);

        // Movie not found ?
        if ($movie === null) {
            throw $this->createNotFoundException('Film ou série non trouvé.');
        }

        // On récupère les castings
        $castingsList = $castingRepository->findAllJoinedToPersonByMovieQb($movie);
        dump($castingsList);

        // On rend le template associé en lui donnant le film
        return $this->render('front/main/movie_show.html.twig', [
            'movie' => $movie,
            'castingsList' => $castingsList,
        ]);
    }

    /**
     * All movies/series list
     * 
     * @Route("/movies/list", name="main_movies_list")
     */
    public function list(MovieRepository $movieRepository)
    {
        // On va chercher les données
        $moviesList = $movieRepository->findAllOrderedByTitleAscQb();

        return $this->render('front/main/list.html.twig', [
            'moviesList' => $moviesList,
        ]);
    }

    /**
     * Theme switcher
     * Toggles Netflix and Allociné themes
     * 
     * @Route("/theme/toggle", name="main_theme_switcher")
     */
    public function themeSwitcher(SessionInterface $session)
    {
        // Notre but est de stocker en session utilisateur
        // le theme choisi

        // On récupère le thème de la session
        $theme = $session->get('theme', 'netflix');

        // On "inverse" le thème
        if ($theme === 'netflix') {
            $session->set('theme', 'allocine');
        } else {
            $session->set('theme', 'netflix');
        }

        // On redirige vers la home
        return $this->redirectToRoute('main_home');

        // Puis dans le template base.html.twig
        // on conditionnera la CSS de la nav selon le thème choisi
    }

    /**
     * Add review to a given movie
     * 
     * @Route("/movie/{id<\d+>}/review/add", name="main_movie_review_add", methods={"GET", "POST"})
     * 
     * Récupération automatique du Movie via son {id}
     * @link https://symfony.com/doc/current/doctrine.html#automatically-fetching-objects-paramconverter
     */
    public function movieReviewAdd(Movie $movie, Request $request, ManagerRegistry $doctrine)
    {
        // On crée l'entité Review
        $review = new Review();

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // On associe la Review au film courant $movie
            $review->setMovie($movie);

            // On dump pour voir
            // dd($review);

            $em = $doctrine->getManager();
            $em->persist($review);
            $em->flush();

            return $this->redirectToRoute('main_movie_show', ['slug' => $movie->getSlug()]);
        }

        return $this->renderForm('front/main/movie_review_add.html.twig', [
            'form' => $form,
            'movie' => $movie,
        ]);
    }
}