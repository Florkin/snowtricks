<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserEditController extends AbstractController
{
    /**
     * @param string $userName
     * @return Response
     * @Route("/utilisateurs/modifier/{userName}")
     */
    public function displayUserEditPage(string $userName) : Response
    {
        return $this->render("pages/user-edit.html.twig", [
            "page" => [
                "title" => $userName . " edit"
            ],
        ]);
    }
}