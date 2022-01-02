<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Imagick\Commands;

class PixelateCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Applies a pixelation effect to a given image
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $size = $this->argument(0)->type('digit')->value(10);

        $width = $image->getWidth();
        $height = $image->getHeight();

        $image->getCore()->scaleImage(max(1, ($width / $size)), max(1, ($height / $size)));
        $image->getCore()->scaleImage($width, $height);

        return true;
    }
}
