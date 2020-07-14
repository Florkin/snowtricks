<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TricksListController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Liste des tricks";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "tricks_list";


    /**
     * @return Response
     * @Route("/tricks")
     */
    public function index() : Response
    {
        return $this->render("pages/tricks-listing.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $this->pageTitle,
            ]
        ]);
    }
}