<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserEditController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "User edit";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "edit";


    /**
     * @param string $userName
     * @return Response
     * @Route("/utilisateurs/modifier/{userName}")
     */
    public function index(string $userName) : Response
    {
        return $this->render("pages/user-edit.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $userName . " - " . $this->pageTitle,
            ],
        ]);
    }
}