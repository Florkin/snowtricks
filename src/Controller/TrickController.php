<?php

namespace App\Controller;

use App\Entity\ChatPost;
use App\Entity\Trick;
use App\Form\ChatType;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TrickController extends AbstractController
{
    const PAGE_SIZE = 9;
    /**
     * @var TrickRepository
     */
    private $trickRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var int
     */

    /**
     * TrickController constructor.
     * @param TrickRepository $trickRepository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     */
    public function __construct(TrickRepository $trickRepository, EntityManagerInterface $entityManager, Security $security)
    {
        $this->trickRepository = $trickRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/tricks/liste/{page}", name="trick.index", requirements={"page" = "\d+"})
     * @param int $page
     * @return Response
     */
    public function index(int $page = 1)
    {
        $tricks = $this->trickRepository->findVisibleByPage($page, Self::PAGE_SIZE);

        return $this->render("trick/index.html.twig", [
            'current_menu' => 'trick.index',
            'page' => [
                'title' => 'Liste des tricks',
            ],
            'pagination' => [
                'page' => $page
            ],
            'tricks' => $tricks,
        ]);
    }

    /**
     * @param string $slug
     * @param Trick $trick
     * @param Request $request
     * @return Response
     * @Route("/tricks/{id}-{slug}", name="trick.show", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"})
     */
    public function show(string $slug, Trick $trick, Request $request): Response
    {
        $trickSlug = $trick->getSlug();

        // If slug is wrong, use id to get it and redirect to the right trick
        if ($trickSlug !== $slug) {
            return $this->redirectToRoute("trick.show", [
                "id" => $trick->getId(),
                "slug" => $trickSlug
            ], 301);
        }

        $chatPost = new ChatPost();
        $form = $this->createForm(ChatType::class, $chatPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processPostForm($chatPost, $trick);
        }

        return $this->render("trick/show.html.twig", [
            "current_menu" => "trick.show",
            "page" => [
                "title" => $trick->getTitle(),
            ],
            "trick" => $trick,
            "form" => $form->createView(),
        ]);
    }

    public function processPostForm($chatPost, $trick)
    {
        $chatPost->setTrick($trick);
        $chatPost->setUser($this->security->getUser());
        $this->entityManager->persist($chatPost);
        $this->entityManager->flush();
        $this->addFlash("success", "Votre message a bien été ajouté");
        return $this->redirectToRoute("trick.show", ["id" => $trick->getId(), "slug" => $trick->getSlug()]);
    }

    /**
     * @Route("/tricks/load/{page}/{category_id}", name="ajax.loadmore", requirements={"page" = "\d+", "category_id" = "\d+"}, methods="GET", options = {"expose" = true})
     * @param int $page
     * @param int|null $category_id
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function ajaxLoadMore(int $page, int $category_id = null, Request $request, CategoryRepository $categoryRepository)
    {
        $category = null;

        if ($category_id) {
            $category = $categoryRepository->find($category_id);
        }

        $tricks = $this->trickRepository->findVisibleByPage($page, Self::PAGE_SIZE, $category_id);

        $html = $this->render("_partials/_listing.html.twig", [
            'tricks' => $tricks,
            'category' => $category
        ])->getContent();

        $response = [
            "code" => 200,
            "html" => $html,
            "page" => $page
        ];

        return new JsonResponse($response);
    }

}