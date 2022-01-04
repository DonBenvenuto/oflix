<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Manage user favorites movies and series
 */
class FavoritesManager
{
    // @link https://symfony.com/doc/current/session.html#basic-usage
    private $session;
    // Pour logguer des infos dans var/log/dev.log
    private $logger;
    // Permet d'activer ou non le vidage des favoris
    private $emptyEnabled;

    public function __construct(RequestStack $requestStack, LoggerInterface $logger, bool $emptyEnabled)
    {
        $this->session = $requestStack->getSession();
        $this->logger = $logger;
        $this->emptyEnabled = $emptyEnabled;
    }

    /**
     * Add or Remove a movie from the list
     */
    public function toggle($movie)
    {
        // 1. On récupère les favoris en session, ou un tableau vide sinon
        $myFavorites = $this->session->get('myFavorites', []);

        // 2. On met à jour le tableau récupéré

        // Si l'index du film est déjà dans les favoris
        if (array_key_exists($movie->getId(), $myFavorites)) {
            // On le retire via PHP unset(élément)
            unset($myFavorites[$movie->getId()]);
            // Action à retourner au contrôleur
            $action = 'remove';
            // Log
            $this->logger->info('Favorites, movie removed', ['movie' => $movie->getTitle()]);
        } else {
            // Sinon on l'ajoute
            $myFavorites[$movie->getId()] = $movie;
            // Action à retourner au contrôleur
            $action = 'add';
            // Log
            $this->logger->info('Favorites, movie added', ['movie' => $movie->getTitle()]);
        }

        // 3. On écrase les favoris en session
        $this->session->set('myFavorites', $myFavorites);

        return $action;
    }

    /**
     * Empty list
     */
    public function empty()
    {
        // Le service est-il configuré pour autorisé le vidage ?
        if (!$this->emptyEnabled) {
            return false;
        }

        $this->session->remove('myFavorites');

        return true;
    }
}