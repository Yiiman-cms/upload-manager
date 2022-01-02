<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Imagick\Commands;

class GreyscaleCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Turns an image into a greyscale version
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        return $image->getCore()->modulateImage(100, 0, 100);
    }
}
