<?php

namespace App\Controller\Api;

use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Genre;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Movie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class MovieController extends AbstractController
{
    /**
     * Get movies collection
     * 
     * @Route("/api/movies", name="api_movies_get", methods={"GET"})
     */
    public function getCollection(MovieRepository $movieRepository): Response
    {
        // @todo : retourner les films de la BDD
        
        // On va chercher les données
        $moviesList = $movieRepository->findAll();

        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $moviesList,
            // Le status code
            200,
            // Les en-têtes de réponse à ajouter (aucune)
            [],
            // Les groupes à utiliser par le Serializer
            ['groups' => 'get_collection']
        );
    }

    /**
     * Get one item
     * 
     * @Route("/api/movies/{id<\d+>}", name="api_movie_get_item", methods={"GET"})
     */
    public function getItem(Movie $movie): Response
    {
        // @ todo retourner un film et ses inforamtions détaillées avec son id

        // 404 à faire

        return $this->json(
            $movie,
            Response::HTTP_OK,
            [],
            ['groups' => ['get_item', 'get_collection']]
        );

    }

    /**
     * Get one random movie
     * 
     * @Route("/api/movies/random", name="api_movies_get_item_random", methods={"GET"})
     */
    public function getItemRandom(MovieRepository $movieRepository): Response
    {
        // On va chercher le film
        $randomMovie = $movieRepository->findOneRandomMovie();

        return $this->json(
            $randomMovie,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_item']
        );
    }

    /**
     * @Route("/api/genres/{id<\d+>}/movies", name="api_genres_get_movies", methods={"GET"})
     */
    public function getItemAndMovies(Genre $genre, MovieRepository $movieRepository): Response
    {
        $moviesList = $genre->getMovies();
        //$moviesList = $movieRepository->findBy(['genres' => $genre]);

        // Tableau PHP à convertir en JSON
        $data = [
            'genre' => $genre,
            'movies' => $moviesList,
        ];

        return $this->json(
            $data,
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    // Le groupe des films
                    'get_collection',
                    // Le groupe des genres
                    'get_genres_collection'
                ]
            ]
        );
    }

     /**
     * Create movie item
     * 
     * @Route("/api/movies", name="api_movies_post", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine)
    {
        // Récupérer le contenu JSON
        $jsonContent = $request->getContent();

        // Désérialiser (convertir) le JSON en entité Doctrine Movie
        $movie = $serializer->deserialize($jsonContent, Movie::class, 'json');

        // Valider l'entité

        // On sauvegarde l'entité
        $entityManager = $doctrine->getManager();
        $entityManager->persist($movie);
        $entityManager->flush();

        // On retourne la réponse adaptée (201 + Location: URL de la ressource)
        return $this->json(
            // Le film créé peut être ajouté au retour
            $movie,
            // Le status code : 201 CREATED
            // utilisons les constantes de classes !
            Response::HTTP_CREATED,
            // REST demande un header Location + URL de la ressource créée
            [
            // Nom de l'en-tête + URL
            'Location' => $this->generateUrl('api_movies_post', ['id' => $movie->getId()])
            ],
            // Groups
            ['groups' => 'get_item']
        );
    }

}
