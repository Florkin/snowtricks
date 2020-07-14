<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "User profile";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "user_profile";


    /**
     * @param string $userName
     * @return Response
     * @Route("/utilisateurs/{userName}")
     */
    public function index(string $userName) : Response
    {
        return $this->render("pages/user-profile.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" =>$userName . " - " . $this->pageTitle,
            ],
        ]);
    }
}