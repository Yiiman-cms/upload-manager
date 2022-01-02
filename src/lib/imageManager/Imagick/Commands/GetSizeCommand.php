<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Imagick\Commands;

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
        /** @var \Imagick $core */
        $core = $image->getCore();

        $this->setOutput(new Size(
            $core->getImageWidth(),
            $core->getImageHeight()
        ));

        return true;
    }
}
