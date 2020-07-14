<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickEditController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Trick edit page";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "trick_edit";


    /**
     * @param string $trickName
     * @return Response
     * @Route("/tricks/modifier/{trickName}")
     */
    public function index(string $trickName) : Response
    {
        return $this->render("pages/trick-edit.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $trickName . " - " . $this->pageTitle,
            ],
        ]);
    }
}