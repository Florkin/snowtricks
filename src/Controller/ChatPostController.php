<?php

namespace App\Controller;

use App\Entity\ChatPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatPostController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ChatPostController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
}
