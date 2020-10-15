<?php

namespace App\Controller\Manage;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManageTrickController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private $trickRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AdminTrickController constructor.
     * @param TrickRepository $trickRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TrickRepository $trickRepository, EntityManagerInterface $entityManager)
    {
        $this->trickRepository = $trickRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @Route("/manage/trick/liste", name="manage.trick.index", options = {"expose" = true})
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $tricks = $paginator->paginate(
            $this->trickRepository->findAll(),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render("manage/trick/index.html.twig", [
            'current_menu' => 'manage.trick.index',
            'page' => [
                "title" => 'Gestion des tricks',
            ],
            'tricks' => $tricks
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/manage/trick/nouveau", name="manage.trick.new")
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setAuthor($this->getUser());
            $this->entityManager->persist($trick);
            $this->entityManager->flush();
            $this->addFlash("success", "Le trick " . $trick->getTitle() . " a bien été ajouté");
            return $this->redirectToRoute("manage.trick.index");
        }

        return $this->render("manage/trick/form.html.twig", [
            "current_menu" => "manage.trick.new",
            "page" => [
                "title" => "Nouveau Trick",
            ],
            "trick" => $trick,
            "form" => $form->createView(),
            "btn_label" => "Créer",
        ]);
    }

    /**
     * @param string $slug
     * @param Trick $trick
     * @param Request $request
     * @return Response
     * @Route("/manage/trick/{id}-{slug}", name="manage.trick.edit", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"}, methods="GET|POST")
     */
    public function edit(string $slug, Trick $trick, Request $request): Response
    {
        $trickSlug = $trick->getSlug();

        // If slug is wrong, use id to get it and redirect to the right trick
        if ($trickSlug !== $slug) {
            return $this->redirectToRoute("manage.trick.edit", [
                "id" => $trick->getId(),
                "slug" => $trickSlug
            ], 301);
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdatedBy($this->getUser());
            // Set update date
            $date = new \DateTime();
            $trick->setDateUpdate($date);

            $this->entityManager->flush();
            $this->addFlash("success", "Le trick " . $trick->getTitle() . " a bien été modifié");
            return $this->redirectToRoute("manage.trick.index");
        }

        return $this->render("manage/trick/form.html.twig", [
            "page" => [
                "title" => $trick->getTitle() . ' - Edition',
            ],
            "form" => $form->createView(),
            "trick" => $trick,
            "btn_label" => "Modifier",
        ]);
    }

    /**
     * @param Trick $trick
     * @param Request $request
     * @return Response
     * @Route("/manage/trick/{id}", name="manage.trick.delete", requirements={"id": "[0-9]*"}, methods="DELETE")
     */
    public function delete(Trick $trick, Request $request): Response
    {
        if ($this->isCsrfTokenValid("delete" . $trick->getId(), $request->get("_token"))) {
            $this->entityManager->remove($trick);
            $this->entityManager->flush();
            $this->addFlash("success", "Le trick " . $trick->getTitle() . " a bien été supprimé");
        }

        return $this->redirectToRoute("manage.trick.index");
    }

}