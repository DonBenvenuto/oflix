<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Service\FavoritesManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavoritesController extends AbstractController
{
    /**
     * User favorites list
     * 
     * @Route("/favorites", name="favorites_list")
     */
    public function list()
    {
        return $this->render('front/favorites/list.html.twig');
    }

    /**
     * Favorite gestion
     * <3 Nicolas Lenne
     * 
     * @Route("/favorites/gestion/{id}", name="favorite_gestion", requirements={"id"="\d+"})
     */
    public function favorite(Movie $movie, FavoritesManager $favoritesManager)
    {
        $action = $favoritesManager->toggle($movie);
        
        if ($action == 'add') {
            $this->addFlash('success', $movie->getTitle() . ' ajouté(e) aux favoris');
        } else {
            $this->addFlash('success', $movie->getTitle() . ' retiré(e) des favoris');
        }

        // On redirige vers la home
        return $this->redirectToRoute('favorites_list');
    }

    /**
     * Favorite gestion, vider la liste
     * 
     * @Route("/favorites/gestion/purge", name="favorites_purge")
     */
    public function purgeFavorites(FavoritesManager $favoritesManager)
    {
        // On vide l'attribut de session concerné
        if ($favoritesManager->empty()) {
            $this->addFlash('success', 'Favoris supprimés');
        } else {
            $this->addFlash('danger', 'Cette fonctionnalité est désactivée');
        }

        // On redirige vers la liste
        return $this->redirectToRoute('favorites_list');
    }
}
