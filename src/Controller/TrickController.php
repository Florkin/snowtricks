<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Trick";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "trick";


    /**
     * @param int $id
     * @param string $trickName
     * @param TrickRepository $trickRepository
     * @return Response
     * @Route("/tricks/{id}-{trickName}")
     */
    public function index(int $id, string $trickName, TrickRepository $trickRepository) : Response
    {
        $trick = $trickRepository->find($id);

        return $this->render("pages/trick.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $trickName . " - " . $this->pageTitle,
            ],
            "trick" => $trick,
        ]);
    }
}