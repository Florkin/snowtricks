<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    const PAGE_SIZE = 9;
    /**
     * @var TrickRepository
     */
    private $trickRepository;
    /**
     * @var int
     */

    /**
     * TrickController constructor.
     * @param TrickRepository $trickRepository
     */
    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/tricks/liste/{page}", name="trick.index", requirements={"page" = "\d+"})
     * @param int $page
     * @return Response
     */
    public function index(int $page = 1)
    {
        $tricks = $this->trickRepository->findVisibleByPage($page, Self::PAGE_SIZE);

        return $this->render("trick/index.html.twig", [
            'current_menu' => 'trick.index',
            'page' => [
                'title' => 'Liste des tricks',
            ],
            'pagination' => [
                'page' => $page
            ],
            'tricks' => $tricks,
        ]);
    }

    /**
     * @param string $slug
     * @param Trick $trick
     * @return Response
     * @Route("/tricks/{id}-{slug}", name="trick.show", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"})
     */
    public function show(string $slug, Trick $trick): Response
    {
        $trickSlug = $trick->getSlug();

        // If slug is wrong, use id to get it and redirect to the right trick
        if ($trickSlug !== $slug) {
            return $this->redirectToRoute("trick.show", [
                "id" => $trick->getId(),
                "slug" => $trickSlug
            ], 301);
        }

        return $this->render("trick/show.html.twig", [
            "current_menu" => "trick.show",
            "page" => [
                "title" => $trick->getTitle(),
            ],
            "trick" => $trick,
        ]);
    }

    /**
     * @Route("/tricks/load/{page}/{category_id}", name="ajax.loadmore", requirements={"page" = "\d+", "category_id" = "\d+"}, methods="GET", options = {"expose" = true})
     * @param int $page
     * @param int|null $category_id
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function ajaxLoadMore(int $page, int $category_id = null, Request $request, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($category_id);
        $tricks = $this->trickRepository->findVisibleByPage($page, Self::PAGE_SIZE, $category_id);

        $html = $this->render("_partials/_listing.html.twig", [
            'tricks' => $tricks,
            'category' => $category,
        ])->getContent();

        $response = [
            "code" => 200,
            "html" => $html,
            "page" => $page
        ];

        return new JsonResponse($response);
    }

}