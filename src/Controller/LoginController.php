<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Login page";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "login";


    /**
     * @return Response
     * @Route("/login", name="login")
     */
    public function index() : Response
    {
        return $this->render("pages/login.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $this->pageTitle,
            ]
        ]);
    }
}