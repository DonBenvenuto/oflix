<?php

namespace App\Controller\Api;

use App\Repository\MovieRepository;
use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * @Route("/api/genres", name="api_genres_get", methods ={"GET"})
     */
    public function getCollection(GenreRepository $genreRepository): Response
    {

        $genresList = $genreRepository->findAll();

        return $this->json(
            $genresList, Response::HTTP_OK, [], ['groups' => 'get_collection']);
    
    }


    /**
     * @Route("/api/genres/{id}/movies", name="api_genres_get_movies", methods={"GET"})
     */
    public function getItemAndMovies(Genre $genre, MovieRepository $movieRepository): Response
    {
        $moviesList = $genre->getMovies();
        //$moviesList = $movieRepository->findBy(['genres' => $genre]);

        return $this->json($moviesList, Response::HTTP_OK, [], ['groups' => 'get_collection']);
    }
}
