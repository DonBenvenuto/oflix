<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Twig\Environment as Twig;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    private $maintenanceEnabled;

    public function __construct(bool $maintenanceEnabled)
    {
        $this->maintenanceEnabled = $maintenanceEnabled;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        // Y'a-t-il une maintenance ?
        if ($this->maintenanceEnabled === false) {
            return;
        }
        
        // Avec l'ErrorController, on a une sous-requête
        // qui se charge de générer le HTML de la page d'erreur
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }

        // La réponse
        $response = $event->getResponse();
        // Le contenu de la réponse
        $content = $event->getResponse()->getContent();

        // On ajoute le code de la bannière après la balise body du contenu HTML
        $newHtml = str_replace(
            // Qu'est-ce qu'on cherche ?
            '</nav>',
            // Par quoi on remplace ?
            '</nav><div class="alert alert-danger m-2">Maintenance prévue mardi 10 janvier à 17h00</div>',
            // Dans quelle chaine ?
            $content
        );

        // On assigne le nouveau contenu à la réponse
        $response->setContent($newHtml);

        // /!\ Nul besoin de retourner quoique ce soit ou d'appeler une méthode spécifique
        // l'objet $reponse a été manipulé directement et sera envoyé par le Kernel
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
