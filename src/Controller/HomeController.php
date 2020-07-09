<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @return Response
     * @Route("/")
     */
    public function displayHomePage() : Response
    {
        return $this->render("pages/home.html.twig", [
            "page" => [
                "title" => "Accueil SnowTricks"
            ]
        ]);
    }
}