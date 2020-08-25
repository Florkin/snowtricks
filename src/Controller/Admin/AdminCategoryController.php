<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategoryController extends AbstractController
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
     * AdminTrickController constructor.
     * @param CategoryRepository $categoryRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @Route("/admin/categories/liste", name="admin.category.index")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $paginator->paginate(
            $this->categoryRepository->findAll(),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render("admin/category/index.html.twig", [
            'current_menu' => 'admin.category.index',
            'page' => [
                "title" => 'Gestion des catégories',
            ],
            'categories' => $categories
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/admin/categorie/ajouter", name="admin.category.new")
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            $this->addFlash("success", "La catégorie ". $category->getTitle() ." a bien été ajouté");
            return $this->redirectToRoute("admin.category.index");
        }

        return $this->render("admin/category/form.html.twig", [
            "current_menu" => "admin.category.new",
            "page" => [
                "title" => "Ajouter une catégorie",
            ],
            "category" => $category,
            "form" => $form->createView(),
            "btn_label" => "Créer"
        ]);
    }

    /**
     * @param string $slug
     * @param Category $category
     * @param Request $request
     * @return Response
     * @Route("/admin/categorie/{id}-{slug}", name="admin.category.edit", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"}, methods="GET|POST")
     */
    public function edit(string $slug, Category $category, Request $request): Response
    {
        $categorySlug = $category->getSlug();

        // If slug is wrong, use id to get it and redirect to the right category
        if ($categorySlug !== $slug) {
            return $this->redirectToRoute("admin.category.edit", [
                "id" => $category->getId(),
                "slug" => $categorySlug
            ], 301);
        }

        $form = $this->createForm(categoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash("success", "La categorie ". $category->getTitle() ." a bien été modifiée");
            return $this->redirectToRoute("admin.category.index");
        }

        return $this->render("admin/category/form.html.twig", [
            "page" => [
                "title" => $category->getTitle() . ' - Edition',
            ],
            "form" => $form->createView(),
            "category" => $category,
            "btn_label" => "Modifier"
        ]);
    }

    /**
     * @param Category $category
     * @param Request $request
     * @return Response
     * @Route("/admin/categorie/{id}", name="admin.category.delete", requirements={"id": "[0-9]*"}, methods="DELETE")
     */
    public function delete(Category $category, Request $request): Response
    {
        if ($this->isCsrfTokenValid("delete". $category->getId(), $request->get("_token"))){
            $this->entityManager->remove($category);
            $this->entityManager->flush();
            $this->addFlash("success", "La categorie ". $category->getTitle() ." a bien été supprimée");
        }

        return $this->redirectToRoute("admin.category.index");
    }
}