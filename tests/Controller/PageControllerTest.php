<?php

namespace App\Tests\Controller;

use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
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
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
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
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
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
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testTrickAdd()
    {
        $userId = array_rand($this->users, 1);
        $user = $this->users[$userId];
        $this->client->loginUser($user);
        $crawler = $this->client->request(Request::METHOD_GET, $this->router->generate('manage.trick.new'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=trick]")->form();
        $csrfToken = $form->get("trick")["_token"]->getValue();

        $crawler = $this->client->submitForm(
            "new_trick_submit",
            [
                "trick[_token]" => $csrfToken,
                "trick[title]" => "title test",
                "trick[description]" => "Lorem ipsum dolor sit amet",
                "trick[difficulty]" => 3,
                "trick[categories]" => [
                    0 => 3,
                    1 => 2,
                    2 => 5,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}


