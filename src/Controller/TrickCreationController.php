<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickCreationController extends AbstractController
{
    /**
     * @return Response
     * @Route("/tricks/creation")
     */
    public function displayTrickCreationPage() : Response
    {
        return $this->render("pages/trick-creation.html.twig", [
            "page" => [
                "title" => "Trick creation page"
            ]
        ]);
    }
}