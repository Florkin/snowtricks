<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickPresentationController extends AbstractController
{
    /**
     * @param string $trickName
     * @return Response
     * @Route("/tricks/{trickName}")
     */
    public function displayTrickPresentationPage(string $trickName) : Response
    {
        return $this->render("pages/trick-presentation.html.twig", [
            "page" => [
                "title" => $trickName
            ],
        ]);
    }
}