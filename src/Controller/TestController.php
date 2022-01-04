<?php

namespace App\Controller;

use DateTime;
use App\Entity\Movie;
use App\Entity\Season;
use App\Repository\MovieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    /**
     * Create movie
     * 
     * @Route("/test/movie/create")
     */
    public function create(ManagerRegistry $doctrine): Response
    {
        // On crée une entité "Doctrine"
        $movie = new Movie;
        $movie->setTitle('Avatar');
        $movie->setType('Film');
        $movie->setReleaseDate(new DateTime('2009-12-24'));
        dump($movie);

        // On va faire appel au Manager de Doctrine
        $entityManager = $doctrine->getManager();
        // Prépare-toi à "persister" notre objet (req. INSERT INTO)
        $entityManager->persist($movie);

        // On exécute les requêtes SQL
        $entityManager->flush();

        dd($movie);
    }

    /**
     * List movies
     * 
     * @Route("/test/movie/list")
     */
    public function list(MovieRepository $movieRepository)
    {
        $moviesList = $movieRepository->findAll();

        dd($moviesList);
    }

    /**
     * Movie show
     * 
     * @Route("/test/movie/{id}", requirements={"id"="\d+"})
     */
    public function show($id, MovieRepository $movieRepository)
    {
        $movie = $movieRepository->find($id);

        // @todo Gérer la 404

        dump($movie);

        return $this->render('test/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * Movie update
     * 
     * @Route("/test/movie/update/{id}", requirements={"id"="\d+"})
     */
    public function update($id, MovieRepository $movieRepository, ManagerRegistry $doctrine)
    {
        // On va chercher l'enregistrement
        $movie = $movieRepository->find($id);

        // @todo Gérer la 404

        // On le modifie (titre au hasard)
        $movie->setTitle('Avatar ' . mt_rand(2, 99));

        // On le met à jour via le Manager
        $entityManager = $doctrine->getManager();
        // Exécute la requête d'UPDATE
        $entityManager->flush();

        dd($movie);
    }

    /**
     * Movie delete
     * 
     * @Route("/test/movie/delete/{id}", requirements={"id"="\d+"})
     */
    public function delete($id, MovieRepository $movieRepository, ManagerRegistry $doctrine)
    {
        // On va chercher l'enregistrement
        $movie = $movieRepository->find($id);

        // @todo Gérer la 404

        // On demande au Manager de le supprimer
        $entityManager = $doctrine->getManager();
        $entityManager->remove($movie);
        // Exécute la requête d'UPDATE
        $entityManager->flush();

        dd($movie);
    }

    /**
     * Création d'une saison et ajout à une série existante
     * 
     * @Route("/test/season/add")
     */
    public function seasonAdd(MovieRepository $movieRepository, ManagerRegistry $doctrine)
    {
        // On va récupérer la série voulue via le MovieRepository
        $xfiles = $movieRepository->find(3); // X-files = film #3

        // On crée une nouvelle saison
        $season = new Season;

        // On renseigne les informations nécessaires
        $season->setNumber(3); // La saison 1
        $season->setEpisodesNumber(23); // 8 épisodes
        // La série associée
        $season->setMovie($xfiles);

        dump($season, $xfiles);

        // On fait appel à l'entity Manager de Doctrine
        $entityManager = $doctrine->getManager();
        // On persiste $season
        $entityManager->persist($season);
        // Exécute les requêtes SQL
        $entityManager->flush();

        dd($season);
    }

}
