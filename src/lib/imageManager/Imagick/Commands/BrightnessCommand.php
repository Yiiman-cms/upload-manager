<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Imagick\Commands;

class BrightnessCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Changes image brightness
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $level = $this->argument(0)->between(-100, 100)->required()->value();

        return $image->getCore()->modulateImage(100 + $level, 100, 100);
    }
}
