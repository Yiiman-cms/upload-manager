<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Imagick\Commands;

use YiiMan\LibUploadManager\lib\imageManager\Imagick\Color;

class RotateCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Rotates image counter clockwise
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $angle = $this->argument(0)->type('numeric')->required()->value();
        $color = $this->argument(1)->value();
        $color = new Color($color);

        // restrict rotations beyond 360 degrees, since the end result is the same
        $angle %= 360;

        // rotate image
        $image->getCore()->rotateImage($color->getPixel(), ($angle * -1));

        return true;
    }
}
