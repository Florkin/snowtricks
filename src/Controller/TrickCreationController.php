<?php

namespace App\Controller;

use App\Entity\Trick;
use Doctrine\ORM\Query\AST\Functions\CurrentTimeFunction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickCreationController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Trick creation page";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "trick_create";


    /**
     * @return Response
     * @Route("/tricks/creation")
     */
    public function index() : Response
    {
        return $this->render("pages/trick-creation.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $this->pageTitle,
            ]
        ]);
    }
}