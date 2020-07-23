<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Trick";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "trick";
    /**
     * @var TrickRepository
     */
    private $repository;

    /**
     * TrickController constructor.
     * @param TrickRepository $repository
     */
    public function __construct(TrickRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param int $id
     * @param string $slug
     * @param Trick $trick
     * @return Response
     * @Route("/tricks/{id}-{slug}", name="trick.show", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"})
     */
    public function show(int $id, string $slug, Trick $trick) : Response
    {
        $trickSlug = $trick->getSlug();

        // If slug is wrong, use id to get it and redirect to the right trick
        if ($trickSlug !== $slug){
            return $this->redirectToRoute("trick.show", [
                "id" => $trick->getId(),
                "slug" => $trickSlug
            ], 301);
        }

        return $this->render("pages/trick.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $slug . " - " . $this->pageTitle,
            ],
            "trick" => $trick,
        ]);
    }
}