<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    /**
     * @param string $userName
     * @return Response
     * @Route("/utilisateurs/{userName}")
     */
    public function displayUserProfilePage(string $userName) : Response
    {
        return $this->render("pages/user-profile.html.twig", [
            "page" => [
                "title" => $userName
            ],
        ]);
    }
}