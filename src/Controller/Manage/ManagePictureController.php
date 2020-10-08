<?php

namespace App\Controller\Manage;

use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManagePictureController extends AbstractController
{
    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * AdminPictureController constructor.
     * @param FileUploader $fileUploader
     */
    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
        $this->fileUploader->setTargetDirectory("uploads/images/tricks");
    }

    /**
     * @Route("/manage/picture/upload", name="ajax.picture.upload", methods="GET|POST", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function ajaxUploadImage(Request $request)
    {
        $files = $request->files->all();
        $fileName = $this->fileUploader->upload($files["pictureFiles"]);

        return $this->json($fileName);
    }

    /**
     * @Route("/manage/picture/delete/{filename}", name="ajax.picture.delete", methods="DELETE", options={"expose"=true})
     * @param string $filename
     * @return Response
     */
    public function ajaxDeleteImage(string $filename)
    {
        $this->fileUploader->delete($filename);
        return $this->json("image " . $filename . " successfully deleted");
    }
}