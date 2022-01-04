<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
}
