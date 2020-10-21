<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    const PAGE_SIZE = 15;

    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Accueil SnowTricks";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "home";
    /**
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * HomeController constructor.
     * @param TrickRepository $trickRepository
     */
    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
    }


    /**
     * @return Response
     * @Route("/", name="home")
     */
    public function show() : Response
    {
        $tricks = $this->trickRepository->findVisibleByPage(1, Self::PAGE_SIZE);
        $total = $this->trickRepository->howManyTricks();
        $loadmoreBtn = false;
        if ($total > Self::PAGE_SIZE) {
            $loadmoreBtn = true;
        }

        return $this->render("pages/home.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $this->pageTitle,
            ],
            "pagination" => [
                "page" => 1
            ],
            "tricks" => $tricks,
            "loadmoreBtn" => $loadmoreBtn
        ]);
    }
}