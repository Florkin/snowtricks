<?php

namespace App\Controller\Manage;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Handlers\Forms\EntityFormHandler;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ManageCategoryController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var EntityFormHandler
     */
    private $formHandler;

    /**
     * AdminTrickController constructor.
     * @param CategoryRepository $categoryRepository
     * @param EntityManagerInterface $entityManager
     * @param EntityFormHandler $formHandler
     */
    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, EntityFormHandler $formHandler)
    {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
        $this->formHandler = $formHandler;
    }

    /**
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @Route("/manage/categories/liste", name="manage.category.index")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $paginator->paginate(
            $this->categoryRepository->findAll(),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render("manage/category/index.html.twig", [
            'current_menu' => 'manage.category.index',
            'page' => [
                "title" => 'Gestion des catégories',
            ],
            'categories' => $categories
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/manage/categorie/ajouter", name="manage.category.new")
     */
    public function new(Request $request): Response
    {
        $category = new Category();

        if ($this->formHandler->handle($request, $category, CategoryType::class)) {
            $this->addFlash("success", "La catégorie ". $category->getTitle() ." a bien été ajouté");
            return $this->redirectToRoute("manage.category.index");
        }

        return $this->render("manage/category/form.html.twig", [
            "current_menu" => "manage.category.new",
            "page" => [
                "title" => "Ajouter une catégorie",
            ],
            "category" => $category,
            "form" => $this->formHandler->createView(),
            "btn_label" => "Créer"
        ]);
    }

    /**
     * @param string $slug
     * @param Category $category
     * @param Request $request
     * @return Response
     * @Route("/manage/categorie/{id}-{slug}", name="manage.category.edit", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"}, methods="GET|POST")
     */
    public function edit(string $slug, Category $category, Request $request): Response
    {
        $categorySlug = $category->getSlug();

        // If slug is wrong, use id to get it and redirect to the right category
        if ($categorySlug !== $slug) {
            return $this->redirectToRoute("manage.category.edit", [
                "id" => $category->getId(),
                "slug" => $categorySlug
            ], 301);
        }

        if ($this->formHandler->handle($request, $category, CategoryType::class)) {
            $this->addFlash("success", "La categorie ". $category->getTitle() ." a bien été modifiée");
            return $this->redirectToRoute("manage.category.index");
        }

        return $this->render("manage/category/form.html.twig", [
            "page" => [
                "title" => $category->getTitle() . ' - Edition',
            ],
            "form" => $this->formHandler->createView(),
            "category" => $category,
            "btn_label" => "Modifier"
        ]);
    }

    /**
     * @param Category $category
     * @param Request $request
     * @return Response
     * @Route("/manage/categorie/{id}", name="manage.category.delete", requirements={"id": "[0-9]*"}, methods="DELETE")
     */
    public function delete(Category $category, Request $request): Response
    {
        if ($this->isCsrfTokenValid("delete". $category->getId(), $request->get("_token"))){
            $this->entityManager->remove($category);
            $this->entityManager->flush();
            $this->addFlash("success", "La categorie ". $category->getTitle() ." a bien été supprimée");
        }

        return $this->redirectToRoute("manage.category.index");
    }
}