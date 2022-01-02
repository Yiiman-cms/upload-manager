<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Gd\Commands;

use YiiMan\LibUploadManager\lib\imageManager\Size;

class GetSizeCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Reads size of given image instance in pixels
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $this->setOutput(new Size(
            imagesx($image->getCore()),
            imagesy($image->getCore())
        ));

        return true;
    }
}
