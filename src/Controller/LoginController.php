<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @return Response
     * @Route("/login")
     */
    public function displayLoginPage() : Response
    {
        return $this->render("pages/login.html.twig", [
            "page" => [
                "title" => "Login page"
            ]
        ]);
    }
}