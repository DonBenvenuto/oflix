<?php

namespace App\Tests\Service;

use App\Service\OmdbApi;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OmdbApiTest extends KernelTestCase
{
    public function testFetch()
    {
        /**
         * Ce test suffit à s'assurer que : 
         * - Notre clé est valide
         * - L'API retourne le JSON attendu
         */
        // (1) On démarre le Kernel de Symfony
        self::bootKernel();

        // (2) On récupère le conteneur de services
        $container = static::getContainer();

        // On demande au conteneur le service OmdbApi
        $omdbApi = $container->get(OmdbApi::class);

        // On appelle la méthode fetch()
        $result = $omdbApi->fetch('Rambo');

        // On affirme que $result est un tableau
        $this->assertIsArray($result);
        // La clé Title est présente
        $this->assertArrayHasKey('Title', $result);
        // On affirme que la clé Title = Rambo
        $this->assertEquals('Rambo', $result['Title']);
    }
}