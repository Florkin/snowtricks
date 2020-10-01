<?php

namespace App\Service;

use Intervention\Image\ImageManager;

class ImageResizer
{
    function resizeImage($path, $filename, $width, $height, $thumb = false)
    {
        $manager = new ImageManager();
        $img = $manager->make($path . "/" . $filename);
        $img->fit($width, $height);
        if ($thumb == true) {
            //Check if the directory already exists.
            if(!is_dir($path . "/thumbs/")){
                //Directory does not exist, so lets create it.
                mkdir($path . "/thumbs/", 0755);
            }
            $img->save($path . "/thumbs/" . $filename, 80, "webp");
        } else {
            $img->save();
        }
    }
}