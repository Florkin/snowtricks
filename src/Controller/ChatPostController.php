<?php

namespace App\Controller;

use App\Entity\ChatPost;
use App\Repository\CategoryRepository;
use App\Repository\ChatPostRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatPostController extends AbstractController
{
    const PAGE_SIZE = 10;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ChatPostRepository
     */
    private $chatPostRepository;

    /**
     * ChatPostController constructor.
     * @param EntityManagerInterface $entityManager
     * @param ChatPostRepository $chatPostRepository
     */
    public function __construct(EntityManagerInterface $entityManager, ChatPostRepository $chatPostRepository)
    {
        $this->entityManager = $entityManager;
        $this->chatPostRepository = $chatPostRepository;
    }

    /**
     * @param ChatPost $chatPost
     * @param int $trickID
     * @param string $trickSlug
     * @param Request $request
     * @return Response
     * @Route("/chatpost/{trickID}-{trickSlug}/{id}", name="chatpost.delete", requirements={"id": "[0-9]*", "trickID": "[0-9]*"}, methods="DELETE")
     */
    public function delete(ChatPost $chatPost, int $trickID, string $trickSlug, Request $request): Response
    {
        if ($this->isCsrfTokenValid("delete" . $chatPost->getId(), $request->get("_token"))) {
            $this->entityManager->remove($chatPost);
            $this->entityManager->flush();
            $this->addFlash("success", "Le message a bien été supprimé");
        }

        return $this->redirectToRoute("trick.show", ["id" => $trickID, "slug" => $trickSlug]);
    }

    /**
     * @Route("/chatposts/load/{page}/{trick_id}", name="ajax.loadmore.chatposts", requirements={"page" = "\d+", "trick_id" = "\d+"}, methods="GET", options = {"expose" = true})
     * @param int $page
     * @param int|null $trick_id
     * @param Request $request
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function ajaxLoadMore(int $page, int $trick_id, Request $request, TrickRepository $trickRepository)
    {
        // Check if there is entities to load
        $total = $this->chatPostRepository->howManyPosts($trick_id);
        $current = ceil($total / Self::PAGE_SIZE);
        $isLast = false;
        if ((int)$current == (int)$page) {
            $isLast = true;
        }

        $trick = $trickRepository->findOneBy(["id" => $trick_id]);
        $chatPosts = $this->chatPostRepository->findByPage($page, Self::PAGE_SIZE, $trick_id);

        $html = $this->render("_partials/_chatposts-listing.html.twig", [
            'chatposts' => array_reverse($chatPosts),
            'trick' => $trick,
        ])->getContent();

        $response = [
            "code" => 200,
            "html" => $html,
            "page" => $page,
            "isLast" => $isLast
        ];

        return new JsonResponse($response);
    }
}
