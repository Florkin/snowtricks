<?php


namespace App\Controller\Admin;


use App\Repository\PictureRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\Routing\Annotation\Route;

class AdminPictureController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private $trickRepository;
    /**
     * @var PictureRepository
     */
    private $pictureRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AdminPictureController constructor.
     * @param TrickRepository $trickRepository
     * @param PictureRepository $pictureRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TrickRepository $trickRepository, PictureRepository $pictureRepository, EntityManagerInterface $entityManager)
    {
        $this->trickRepository = $trickRepository;
        $this->pictureRepository = $pictureRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/trick/upload-image/{id}", name="ajax.trick.img.upload", requirements={"id": "[0-9]*"}, methods="POST", options = {"expose" = true})
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
     * @Route("/admin/trick/get-uploaded-images/{id}", name="ajax.get.uploaded.images", requirements={"id": "[0-9]*"}, methods="POST", options = {"expose" = true})
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
     * @Route("/admin/trick/remove-uploaded-image/{id}/{id_picture}", name="ajax.remove.image", requirements={"id": "[0-9]*", "id_picture": "[0-9]*"}, methods="DELETE", options = {"expose" = true})
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