<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    const PAGE_SIZE = 15;

    /**
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * HomeController constructor.
     * @param TrickRepository $trickRepository
     */
    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
    }


    /**
     * @return Response
     * @Route("/", name="home")
     */
    public function show() : Response
    {
        $tricks = $this->trickRepository->findVisibleByPage(1, Self::PAGE_SIZE);
        $total = $this->trickRepository->howManyTricks();
        $loadmoreBtn = false;
        if ($total > Self::PAGE_SIZE) {
            $loadmoreBtn = true;
        }

        return $this->render("pages/home.html.twig", [
            'current_menu' => 'home',
            'is_home_page' => true,
            'page' => [
                "title" => 'Accueil SnowTricks',
            ],
            'pagination' => [
                "page" => 1
            ],
            'tricks' => $tricks,
            'loadmoreBtn' => $loadmoreBtn
        ]);
    }
}