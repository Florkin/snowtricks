<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * TrickController constructor.
     * @param TrickRepository $trickRepository
     */
    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/tricks/liste", name="trick.index", requirements={"page" = "\d+"})
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $tricks = $paginator->paginate(
            $this->trickRepository->findAllVisibleQuery(),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render("trick/index.html.twig", [
            'current_menu' => 'trick.index',
            'page' => [
                'title' => 'Liste des tricks',
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
}