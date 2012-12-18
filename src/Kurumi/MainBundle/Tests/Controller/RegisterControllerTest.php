<?php

namespace Kurumi\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    private $crawler;

    private function getFrontPageCrawler()
    {
        if ($this->crawler === null) {
            $client = static::createClient();
            $this->crawler = $client->request('GET', '/');
        }

        return $this->crawler;
    }

    public function testFrontPageForGuestsHasLoginForm()
    {
        $crawler = $this->getFrontPageCrawler();
        $this->assertTrue($crawler->filter('form#user_login')->count() > 0);
    }

    public function testFrontPageForGuestsHasRegisterForm()
    {
        $crawler = $this->getFrontPageCrawler();
        $this->assertTrue($crawler->filter('form#user_register')->count() > 0);
    }

    /**
     * @depends testFrontPageForGuestsHasLoginForm
     */
    public function testFrontPageFailedLoginShowsErrors()
    {
        $crawler = $this->getFrontPageCrawler();
        $client = static::createClient();
        $form = $crawler->filter('form#user_login')->form(
            array(
                'email' => 'invalid email',
                'password' => '',
            )
        );
        $response = $client->submit($form);
        $this->assertTrue($response->filter('p:contains("Bad credentials")')->count() > 0);
    }
}
