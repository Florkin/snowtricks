<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
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
     * @param TrickRepository $trickRepository
     * @return Response
     * @Route("/")
     */
    public function index(TrickRepository $trickRepository) : Response
    {
        $tricks = $trickRepository->findVisibleLatest(3);

        return $this->render("pages/home.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $this->pageTitle,
            ],
            "tricks" => $tricks
        ]);
    }
}