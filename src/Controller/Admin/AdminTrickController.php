<?php

namespace App\Controller\Admin;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminTrickController extends AbstractController
{
    /**
     * @var string
     *
     * Titre de la page
     */
    private $pageTitle = "Gestion des tricks";

    /**
     * @var string
     *
     * Use to check active link on menu
     */
    private $currentMenu = "admin.trick.index";

    /**
     * @var TrickRepository
     */
    private $repository;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * AdminTrickController constructor.
     * @param TrickRepository $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TrickRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * @return Response
     * @Route("/admin/trick/liste", name="admin.trick.index")
     */
    public function index(): Response
    {
        $tricks = $this->repository->findAll();
        return $this->render("admin/admin-trick/index.html.twig", [
            "current_menu" => $this->currentMenu,
            "page" => [
                "title" => $this->pageTitle,
            ],
            "tricks" => $tricks
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/admin/trick/nouveau", name="admin.trick.new")
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($trick);
            $this->entityManager->flush();
            $this->addFlash("success", "Le trick ". $trick->getTitle() ." a bien été ajouté");
            return $this->redirectToRoute("admin.trick.index");
        }

        return $this->render("admin/admin-trick/form.html.twig", [
            "current_menu" => "admin.trick.new",
            "page" => [
                "title" => "Nouveau Trick",
            ],
            "trick" => $trick,
            "form" => $form->createView(),
            "btn_label" => "Créer"
        ]);
    }

    /**
     * @param string $slug
     * @param Trick $trick
     * @param Request $request
     * @return Response
     * @Route("/admin/trick/{id}-{slug}", name="admin.trick.edit", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"}, methods="GET|POST")
     */
    public function edit(string $slug, Trick $trick, Request $request): Response
    {
        $trickSlug = $trick->getSlug();

        // If slug is wrong, use id to get it and redirect to the right trick
        if ($trickSlug !== $slug) {
            return $this->redirectToRoute("admin.trick.edit", [
                "id" => $trick->getId(),
                "slug" => $trickSlug
            ], 301);
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Set update date
            $date = new \DateTime();
            $trick->setDateUpdate($date);

            $this->entityManager->flush();
            $this->addFlash("success", "Le trick ". $trick->getTitle() ." a bien été modifié");
            return $this->redirectToRoute("admin.trick.index");
        }

        return $this->render("admin/admin-trick/form.html.twig", [
            "page" => [
                "title" => $trick->getTitle() . ' - Edition',
            ],
            "form" => $form->createView(),
            "trick" => $trick,
            "btn_label" => "Modifier"
        ]);
    }

    /**
     * @param Trick $trick
     * @param Request $request
     * @return Response
     * @Route("/admin/trick/{id}", name="admin.trick.delete", requirements={"id": "[0-9]*"}, methods="DELETE")
     */
    public function delete(Trick $trick, Request $request): Response
    {
        if ($this->isCsrfTokenValid("delete". $trick->getId(), $request->get("_token"))){
            $this->entityManager->remove($trick);
            $this->entityManager->flush();
            $this->addFlash("success", "Le trick ". $trick->getTitle() ." a bien été supprimé");
        }

        return $this->redirectToRoute("admin.trick.index");
    }
}