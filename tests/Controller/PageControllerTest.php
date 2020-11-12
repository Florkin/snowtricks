<?php

namespace App\Tests\Controller;

use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

class PageControllerTest extends WebTestCase
{
    private $client;

    /**
     * @var Router
     */
    private $router;
    private $tricks;
    private $users;
    private $trickRepository;

    public function setUp()
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer()->get('test.service_container');
        $this->router = $container->get('router');
        $this->trickRepository = $container->get(TrickRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->tricks = $this->trickRepository->findAll();
        $this->users = $this->userRepository->findAll();
    }

    public function testHomePageUp()
    {
        $this->client->request('GET', $this->router->generate('home'));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testTrickModificationNotloggedIn()
    {
        $trickId = array_rand($this->tricks, 1);
        $trick = $this->tricks[$trickId];
        $route = $this->router->generate('manage.trick.edit', ['id' => $trick->getId(), 'slug' => $trick->getSlug()]);
        $this->client->request('GET', $route);
        $this->assertResponseRedirects($this->router->generate('app_login'));
    }

    public function testTrickDeleteNotAuthor()
    {
        $userId = array_rand($this->users, 1);
        $user = $this->users[$userId];
        $trick = $this->trickRepository->findOneByNot("author", $userId)[0];
        $this->client->loginUser($user);
        $route = $this->router->generate('manage.trick.delete', ['id' => $trick->getId(), 'slug' => $trick->getSlug()]);
        $this->client->request('DELETE', $route);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
        $this->client->loginUser($user);
    }

    public function testTrickDeleteIsAuthor()
    {
        $trickId = array_rand($this->tricks);
        $trick = $this->tricks[$trickId];
        $user = $trick->getAuthor();
        $this->client->loginUser($user);
        $route = $this->router->generate('manage.trick.delete', ['id' => $trick->getId(), 'slug' => $trick->getSlug()]);
        $this->client->request('DELETE', $route);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

}


