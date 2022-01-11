<?php

namespace App\Tests\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnonymousAccessTest extends WebTestCase
{
    /**
     * Routes en GET pour Anonymous
     * 
     * @dataProvider getUrls
     */
    public function testPageGetIsRedirect($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertResponseRedirects();
    }

    public function getUrls()
    {
        yield ['/back/casting/movie/1'];
        yield ['/back/casting/new/movie/1'];
        yield ['/back/casting/1/edit'];
        yield ['/back/movie/'];
        yield ['/back/movie/new'];
        yield ['/back/movie/1'];
        yield ['/back/movie/1/edit'];
        yield ['/back/user/'];
        yield ['/back/user/new'];
        yield ['/back/user/1'];
        yield ['/back/user/1/edit'];
        // ...
    }

    /**
     * Routes en POST pour Anonymous
     * 
     * @dataProvider postUrls
     */
    public function testPagePostIsRedirect($url)
    {
        $client = self::createClient();
        $client->request('POST', $url);

        $this->assertResponseRedirects();
    }


    public function postUrls()
    {
        yield ['/back/casting/new/movie/1'];
        yield ['/back/casting/1/edit'];
        yield ['/back/casting/1'];
        yield ['/back/movie/new'];
        yield ['/back/movie/1/edit'];
        yield ['/back/movie/1'];
        yield ['/back/user/new'];
        yield ['/back/user/1/edit'];
        yield ['/back/user/1'];
        // ...
    }
}
