<?php

namespace App\Controller\Admin;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\PictureRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Liip\ImagineBundle\Templating\Helper\FilterHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class AdminTrickController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private $trickRepository;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var PictureRepository
     */
    private $pictureRepository;

    /**
     * AdminTrickController constructor.
     * @param TrickRepository $trickRepository
     * @param PictureRepository $pictureRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TrickRepository $trickRepository, PictureRepository $pictureRepository, EntityManagerInterface $entityManager)
    {
        $this->trickRepository = $trickRepository;
        $this->entityManager = $entityManager;
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @Route("/admin/trick/liste", name="admin.trick.index")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $tricks = $paginator->paginate(
            $this->trickRepository->findAll(),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render("admin/trick/index.html.twig", [
            'current_menu' => 'admin.trick.index',
            'page' => [
                "title" => 'Gestion des tricks',
            ],
            'tricks' => $tricks
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
            $this->addFlash("success", "Le trick " . $trick->getTitle() . " a bien été ajouté");
            return $this->redirectToRoute("admin.trick.index");
        }

        return $this->render("admin/trick/form.html.twig", [
            "current_menu" => "admin.trick.new",
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
            $this->addFlash("success", "Le trick " . $trick->getTitle() . " a bien été modifié");
            return $this->redirectToRoute("admin.trick.index");
        }

        return $this->render("admin/trick/form.html.twig", [
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
     * @Route("/admin/trick/{id}", name="admin.trick.delete", requirements={"id": "[0-9]*"}, methods="DELETE")
     */
    public function delete(Trick $trick, Request $request): Response
    {
        if ($this->isCsrfTokenValid("delete" . $trick->getId(), $request->get("_token"))) {
            $this->entityManager->remove($trick);
            $this->entityManager->flush();
            $this->addFlash("success", "Le trick " . $trick->getTitle() . " a bien été supprimé");
        }

        return $this->redirectToRoute("admin.trick.index");
    }

    /**
     * @Route("/admin/trick/upload-image/{id}", name="ajax.trick.img.upload", requirements={"id": "[0-9]*"}, methods="POST")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function ajaxUploadImage(int $id, Request $request): Response
    {
        $trick = $this->trickRepository->findOneBy(['id' => $id]);
        $trick->setPictureFiles($request->files->all());

        $date = new \DateTime();
        $trick->setDateUpdate($date);

        $this->entityManager->flush();

        $response = [
            'status' => 'success'
        ];

        return $this->json($response);
    }

    /**
     * @Route("/admin/trick/get-uploaded-images/{id}", name="ajax.get.uploaded.images", requirements={"id": "[0-9]*"}, methods="POST")
     * @param int $id
     * @param Request $request
     * @param UploaderHelper $helper
     * @return Response
     */
    public function ajaxGetUploadedImages(int $id, Request $request, UploaderHelper $helper): Response
    {
        $trick = $this->trickRepository->findOneBy(['id' => $id]);
        $pictures = $trick->getPictures();
        $pathArray =[];
        foreach ($pictures as $picture){
            $pathArray[$picture->getId()] =  $helper->asset($picture, 'imageFile');
        }

        return $this->json($pathArray);
    }

    /**
     * @Route("/admin/trick/remove-uploaded-image/{id}/{id_picture}", name="ajax.remove.image", requirements={"id": "[0-9]*", "id_picture": "[0-9]*"}, methods="POST")
     * @param int $id
     * @param int $id_picture
     * @param Request $request
     * @param UploaderHelper $helper
     * @return Response
     */
    public function ajaxRemoveImage(int $id, $id_picture = 0): Response
    {
        $trick = $this->trickRepository->findOneBy(['id' => $id]);
        $picture = $this->pictureRepository->findOneBy(['id' => $id_picture]);

        $trick->removePicture($picture);

        $date = new \DateTime();
        $trick->setDateUpdate($date);

        $this->entityManager->flush();

        $response = [
            'status' => 'success'
        ];

        return $this->json($response);
    }
}