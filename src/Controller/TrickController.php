<?php

namespace App\Controller;

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
     * @param string $trickName
     * @return Response
     * @Route("/tricks/{trickName}")
     */
    public function index(string $trickName) : Response
    {
        return $this->render("pages/trick.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $trickName . " - " . $this->pageTitle,
            ],
        ]);
    }
}