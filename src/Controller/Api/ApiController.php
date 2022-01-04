<?php

namespace App\Controller\Api;

use App\Model\Movies;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * Get movies collections
     * 
     * @Route("/api/movies", name="api_movies_get")
     */
    public function movies_get(): Response
    {
        // @todo : retourner les films de la BDD
        
        // On va chercher les données
        $moviesModel = new Movies;
        $moviesList = $moviesModel->getAllMovies();

        return $this->json($moviesList);
    }

    /**
     * Get one movie item
     * 
     * ...
     * 
     */
}
