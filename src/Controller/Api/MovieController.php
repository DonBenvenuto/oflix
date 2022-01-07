<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @Route("/api/movies/{id<\d+>}", name="api_movies_get_item", methods={"GET"})
     */
    public function getItem(Movie $movie)
    {
        // 404 custom à gérer

        return $this->json($movie, Response::HTTP_OK, [], ['groups' => 'get_item']);
    }

    /**
     * Get one random movie
     * 
     * @Route("/api/movies/random", name="api_movies_get_item_random", methods={"GET"})
     */
    public function getItemRandom(MovieRepository $movieRepository): Response
    {
        // On va chercher le film
        // /!\ Attention film "incomplet", vient d'une requête SQL
        $randomMovie = $movieRepository->findOneRandomMovie();

        return $this->json(
            $randomMovie,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_item']
        );
    }

    /**
     * Create movie item
     * 
     * @Route("/api/movies", name="api_movies_post", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        // Récupérer le contenu JSON
        $jsonContent = $request->getContent();

        try {
            // Désérialiser (convertir) le JSON en entité Doctrine Movie
            $movie = $serializer->deserialize($jsonContent, Movie::class, 'json');
        } catch (NotEncodableValueException $e) {
            // Si le JSON fourni est "malformé" ou manquant, on prévient le client
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Valider l'entité
        // @link : https://symfony.com/doc/current/validation.html#using-the-validator-service
        $errors = $validator->validate($movie);

        // Y'a-t-il des erreurs ?
        if (count($errors) > 0) {
            // @todo Retourner des erreurs de validation propres
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

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
            // REST demande un header Location + URL de la ressource
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_movies_get_item', ['id' => $movie->getId()])
            ],
            // Groups
            ['groups' => 'get_item']
        );
    }
}
