<?php

namespace App\Controller\Manage;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Handlers\Forms\EntityFormHandler;
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
     * @var EntityFormHandler
     */
    private $formHandler;

    /**
     * AdminTrickController constructor.
     * @param TrickRepository $trickRepository
     * @param EntityManagerInterface $entityManager
     * @param EntityFormHandler $formHandler
     */
    public function __construct(TrickRepository $trickRepository, EntityManagerInterface $entityManager, EntityFormHandler $formHandler)
    {
        $this->trickRepository = $trickRepository;
        $this->entityManager = $entityManager;
        $this->formHandler = $formHandler;
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
            $this->trickRepository->findAllUnvisible(),
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
        $trick->setAuthor($this->getUser());

        if ($this->formHandler->handle($request, $trick, TrickType::class)) {
            $this->addFlash("success", "Le trick " . $trick->getTitle() . " a bien été ajouté");
            return $this->redirectToRoute("trick.index");
        }

        return $this->render("manage/trick/form.html.twig", [
            "current_menu" => "manage.trick.new",
            "page" => [
                "title" => "Nouveau Trick",
            ],
            "trick" => $trick,
            "form" => $this->formHandler->createView(),
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
        $this->denyAccessUnlessGranted('EDIT', $trick);

        $trickSlug = $trick->getSlug();

        // If slug is wrong, use id to get it and redirect to the right trick
        if ($trickSlug !== $slug) {
            return $this->redirectToRoute("manage.trick.edit", [
                "id" => $trick->getId(),
                "slug" => $trickSlug
            ], 301);
        }

        $trick->setUpdatedBy($this->getUser());
        $trick->setDateUpdate((new \DateTime()));

        if ($this->formHandler->handle($request, $trick, TrickType::class)) {
            $this->addFlash("success", "Le trick " . $trick->getTitle() . " a bien été modifié");
            return $this->redirectToRoute("trick.index");
        }


        return $this->render("manage/trick/form.html.twig", [
            "page" => [
                "title" => $trick->getTitle() . ' - Edition',
            ],
            "form" => $this->formHandler->createView(),
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
        $this->denyAccessUnlessGranted('DELETE', $trick);

        if ($this->isCsrfTokenValid("delete" . $trick->getId(), $request->get("_token"))) {
            $this->entityManager->remove($trick);
            $this->entityManager->flush();
            $this->addFlash("success", "Le trick " . $trick->getTitle() . " a bien été supprimé");
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @param Trick $trick
     * @param Request $request
     * @return Response
     * @Route("/manage/trick/{id}", name="manage.trick.enable", requirements={"id": "[0-9]*"}, methods="ENABLE")
     */
    public function enable(Trick $trick, Request $request): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $trick);

        if ($this->isCsrfTokenValid("enable" . $trick->getId(), $request->get("_token"))) {
            $trick->setVisible(true);
            $this->entityManager->persist($trick);
            $this->entityManager->flush();
            $this->addFlash("success", "Le trick " . $trick->getTitle() . " a bien été activé");
        }

        return $this->redirectToRoute('manage.trick.index');
    }

}