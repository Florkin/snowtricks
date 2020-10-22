<?php

// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getUploadedImage', [$this, 'getUploadedImage']),
            new TwigFunction('getImage', [$this, 'getImage']),
        ];
    }

    public function getUploadedImage($collection, $filename, $thumb = false)
    {
        $path = $thumb == true ? "/uploads/images/" . $collection . "/" . "thumbs/" . $filename : "/uploads/images/" . $collection . "/" . $filename;
        return $path;
    }

    public function getImage($filename)
    {
        $path = "/img/" . $filename;
        return $path;
    }
}
