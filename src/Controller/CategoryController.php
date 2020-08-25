<?php


namespace App\Controller;


use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {

        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $slug
     * @param Category $category
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @Route("/categories/{id}-{slug}", name="category.show", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"})
     */
    public function show(string $slug, Category $category, PaginatorInterface $paginator, Request $request): Response
    {
        $categorySlug = $category->getSlug();

        // If slug is wrong, use id to get it and redirect to the right trick
        if ($categorySlug !== $slug) {
            return $this->redirectToRoute("trick.show", [
                "id" => $category->getId(),
                "slug" => $categorySlug
            ], 301);
        }

        $tricks = $paginator->paginate(
            $category->getRelatedTricks(),
            $request->query->getInt('page', 1),
            12
        );


        return $this->render('category/show.html.twig', [
            'current_menu' => 'category.show.' . $category->getSlug(),
            'page' => [
                'title' => $category->getTitle(),
            ],
            'category' => $category,
            'tricks' => $tricks,
        ]);
    }

    /**
     * @param string $currentMenu
     * @return Response
     */
    public function navbarDropdownList(string $currentMenu = null) : Response
    {
        $categories = $this->categoryRepository->findAllNotEmpty();
        return $this->render('_partials/_categories-dropdown.html.twig', [
            'categories' => $categories,
            'current_menu' => $currentMenu
        ]);
    }
}