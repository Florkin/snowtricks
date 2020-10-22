<?php

namespace App\Controller;

use App\Entity\ChatPost;
use App\Repository\ChatPostRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * ChatPostController constructor.
     * @param EntityManagerInterface $entityManager
     * @param ChatPostRepository $chatPostRepository
     * @param TrickRepository $trickRepository
     */
    public function __construct(EntityManagerInterface $entityManager, ChatPostRepository $chatPostRepository, TrickRepository $trickRepository)
    {
        $this->entityManager = $entityManager;
        $this->chatPostRepository = $chatPostRepository;
        $this->trickRepository = $trickRepository;
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
        $this->denyAccessUnlessGranted("DELETE", $chatPost);
        if ($this->isCsrfTokenValid("delete" . $chatPost->getId(), $request->get("_token"))) {
            $this->entityManager->remove($chatPost);
            $this->entityManager->flush();
            $this->addFlash("success", "Le message a bien été supprimé");
        }

        return $this->redirectToRoute("trick.show", ["id" => $trickID, "slug" => $trickSlug]);
    }

    /**
     * @Route("/chatposts/load/{page}/{trick_id}", name="ajax.chatposts.loadmore", requirements={"page" = "\d+", "trick_id" = "\d+"}, methods="GET", options = {"expose" = true})
     * @param int $page
     * @param int|null $trick_id
     * @param Request $request
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function ajaxLoadMore(int $page, int $trick_id)
    {
        // Check if there is entities to load
        $total = $this->chatPostRepository->howManyPosts($trick_id);
        $current = ceil($total / Self::PAGE_SIZE);
        $isLast = false;
        if ((int)$current == (int)$page) {
            $isLast = true;
        }

        $trick = $this->trickRepository->findOneBy(["id" => $trick_id]);
        $chatPosts = $this->chatPostRepository->findByPage($page, Self::PAGE_SIZE, $trick_id);

        $html = $this->render("_partials/_chatposts-listing.html.twig", [
            'chatposts' => $chatPosts,
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

    /**
     * @Route("/chatposts/new", name="ajax.chatposts.new", methods="POST", options = {"expose" = true})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function ajaxNew(Request $request, ValidatorInterface $validator)
    {
        $message = $request->request->get("message");
        $trick = $this->trickRepository->find($request->request->get("trick_id"));
        $this->denyAccessUnlessGranted('ADD_CHATPOST', $trick);

        $chatPost = new ChatPost();
        $chatPost->setMessage($message);
        $chatPost->setTrick($trick);
        $chatPost->setUser($this->getUser());
        $errors = $validator->validate($chatPost);

        if ($errors->count() > 0) {
            $html = $this->render('_partials/_messages.html.twig', [
                'errors' => $errors
            ])->getContent();

            return new JsonResponse(["html" => $html]);
        }

        $this->entityManager->persist($chatPost);
        $this->entityManager->flush();

        $html = $this->render("_partials/_chatpost.html.twig", [
            'post' => $chatPost,
            'trick' => $trick
        ])->getContent();

        $response = [
            "code" => 200,
            "html" => $html,
        ];

        return new JsonResponse($response);
    }

}
