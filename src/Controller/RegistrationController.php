<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @return Response
     * @Route("/inscription")
     */
    public function displayRegistrationPage() : Response
    {
        return $this->render("pages/registration.html.twig", [
            "page" => [
                "title" => "Registration page"
            ]
        ]);
    }
}