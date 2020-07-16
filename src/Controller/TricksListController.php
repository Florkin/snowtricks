<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TricksListController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Liste des tricks";
    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "tricks_list";
    /**
     * @var TrickRepository
     */
    private $trickRepository;
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * TricksListController constructor.
     * @param TrickRepository $trickRepository
     * @param EntityManager $em
     */
    public function __construct(TrickRepository $trickRepository, EntityManagerInterface $em)
    {
        $this->trickRepository = $trickRepository;
        $this->em = $em;
    }


    /**
     * @return Response
     * @Route("/tricks")
     */
    public function index(): Response
    {
        return $this->render("pages/tricks-listing.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $this->pageTitle,
            ]
        ]);
    }
}