<?php

namespace App\Tests\Controller\Front;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    /**
     * Home
     */
    public function testHome(): void
    {
        // On crée un client
        $client = static::createClient();
        // On exécute une requête HTTP en GET sur l'URL /
        $crawler = $client->request('GET', '/');

        // A-t-on un status code entre 200 et 299
        $this->assertResponseIsSuccessful();
        // Ou status code 200
        // $this->assertResponseStatusCodeSame(200);
        
        // Est-on sur la home ?
        $this->assertSelectorTextContains('h1', 'Films, séries TV et popcorn en illimité.');
    }

    /**
     * Movie show
     */
    public function testMovieShow(): void
    {
        // On crée un client
        $client = static::createClient();
        // On exécute une requête HTTP en GET sur l'URL /film-1
        $crawler = $client->request('GET', '/movie/film-1');

        // Status code 200
        $this->assertResponseStatusCodeSame(200);

        // Est-on sur la home ?
        $this->assertSelectorTextContains('h3', 'Film 1');
    }

    /**
     * Anonymous Add Review
     */
    public function testAnonymousReviewAdd(): void
    {
        // On crée un client
        $client = static::createClient();
        // On exécute une requête HTTP en GET sur l'URL /film-1
        $crawler = $client->request('GET', '/movie/1/review/add');

        // On doit avoir une redirection (status code 302)
        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * ROLE_USER Add Review
     */
    public function testRoleUserReviewAdd(): void
    {
        // On crée un client
        $client = static::createClient();

        // Le Repo des Users
        $userRepository = static::getContainer()->get(UserRepository::class);
        // On récupère user@user.com
        $testUser = $userRepository->findOneByEmail('user@user.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // On exécute une requête HTTP en GET sur l'URL /film-1
        $crawler = $client->request('GET', '/movie/1/review/add');

        // Status code 200 (OK !)
        $this->assertResponseStatusCodeSame(200);
        // Le texte du h1
        $this->assertSelectorTextContains('h1', 'Ajouter une critique');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('btn_submit');

        // @link https://symfony.com/doc/current/testing.html#submitting-forms
        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['review[username]'] = 'Fabien';
        $form['review[email]'] = 'user@user.com';
        $form['review[content]'] = 'retourner des erreurs de validation propresretourner des erreurs de validation propresretourner des erreurs de validation propres';
        $form['review[rating]'] = 2;
        // Reactions est un tableau
        $form['review[reactions]'] = ["smile", "cry"];

        // submit the Form object
        $client->submit($form);

        // Status code 302 OK !
        $this->assertResponseStatusCodeSame(302);
    }
}
