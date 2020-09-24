<?php

namespace App\Controller\Admin;

use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminPictureController extends AbstractController
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
        $this->fileUploader->setTargetDirectory("images/tricks");
    }

    /**
     * @Route("/admin/picture/upload", name="ajax.picture.upload", methods="GET|POST", options={"expose"=true})
     * @param Request $request
     */
    public function ajaxUploadImage(Request $request)
    {
        $files = $request->files->all();
        $fileNames = [];
        foreach ($files as $file) {
            array_push($fileNames, $this->fileUploader->upload($file));
        }

        return $this->json($fileNames);
    }
}