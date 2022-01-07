<?php

namespace App\EventSubscriber;

use App\Repository\MovieRepository;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment as Twig;

class RandomMovieSubscriber implements EventSubscriberInterface
{
    /**
     * Service MovieRepository
     */
    private $movieRepository;

    /**
     * Service Twig
     */
    private $twig;

    public function __construct(MovieRepository $movieRepository, Twig $twig)
    {
        $this->movieRepository = $movieRepository;
        $this->twig = $twig;
    }
    
    public function onKernelController(ControllerEvent $event)
    {

        // dd($event);
        //dump(get_class($event->getController()[0]));

        // On récupère le controlleur depuis l'event
        $controller = $event->getController();

        // Avec les exceptions (404, 403 etc), le contrôleur n'est pas sous forme de tableau
        if (is_array($controller)) {
            // Récupérons le contrôleur, qui se trouve à l'index 0 du tableau
            // qui contient le contrôleur et la méthode à appeler (cf le dump)
            $controller = $controller[0];
        }

        // On récupère le nom de la classe de contrôleur
        $controllerClassName = get_class($controller);

         // On doit exclure les cas où l'on ne souhaite pas exécuter le subscriber

        // Exclure les routes API

        // Exclure les routes de l'admin (vous) et du Profiler et de la Toolbar (JC)
        // soit qui ne commencent pas par 'App\Controller\Front'

        if (strpos($controllerClassName, 'App\Controller\Front') === false) {
            // On sort du suscriber
            return;
        }
        //dump('RandomMovieSubscriber appelé');

        // On va chercher un film au hasard via MovieRepository
        // Option 1 : findAll => random dessus => pas top pour les perfs
        // Option 2 : SQL
        $randomMovie = $this->movieRepository->findOneRandomMovie();

        // On l'envoie en variable globale à Twig
        // @link https://twig.symfony.com/doc/3.x/advanced.html#globals
        $this->twig->addGlobal('randomMovie', $randomMovie);
    }

    public static function getSubscribedEvents()
    {
        return [
            // type d'événement => méthode à appeler
            'kernel.controller' => 'onKernelController',
        ];
    }
}
