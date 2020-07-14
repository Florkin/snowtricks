<?php

namespace App\Controller;

use App\Entity\Trick;
use Doctrine\ORM\Query\AST\Functions\CurrentTimeFunction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickCreationController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Trick creation page";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "trick_create";


    /**
     * @return Response
     * @Route("/tricks/creation")
     */
    public function index() : Response
    {
        $trick = new Trick();
        $trick->setTitle('360 Flip')
            ->setShortDescription('Une description courte')
            ->setDescription('Une description beaucoup beaucoup beaucoup beaucoup beaucoup plus longue')
            ->setDateAdd(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($trick);
        $em->flush();

        return $this->render("pages/trick-creation.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $this->pageTitle,
            ]
        ]);
    }
}