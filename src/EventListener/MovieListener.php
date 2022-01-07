<?php

namespace App\EventListener;

use App\Entity\Movie;
use App\Service\MySlugger;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class MovieListener
{
    private $mySlugger;

    public function __construct(MySlugger $mySlugger)
    {
        $this->mySlugger = $mySlugger;
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function updateSlug(Movie $movie, LifecycleEventArgs $event): void
    {
        // On slugifie le titre
        $movie->setSlug($this->mySlugger->slugify($movie->getTitle()));
    }
}
