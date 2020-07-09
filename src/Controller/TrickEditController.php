<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickEditController extends AbstractController
{
    /**
     * @param string $trickName
     * @return Response
     * @Route("/tricks/modifier/{trickName}")
     */
    public function displayTrickEditPage(string $trickName) : Response
    {
        return $this->render("pages/trick-edit.html.twig", [
            "page" => [
                "title" => $trickName . " edit"
            ],
        ]);
    }
}