<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Registration page";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "registration";


    /**
     * @return Response
     * @Route("/inscription")
     */
    public function index() : Response
    {
        return $this->render("pages/registration.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $this->pageTitle,
            ]
        ]);
    }
}