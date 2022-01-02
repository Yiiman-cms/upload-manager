<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Commands;

use Closure;

class EllipseCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Draws ellipse on given image
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $width = $this->argument(0)->type('numeric')->required()->value();
        $height = $this->argument(1)->type('numeric')->required()->value();
        $x = $this->argument(2)->type('numeric')->required()->value();
        $y = $this->argument(3)->type('numeric')->required()->value();
        $callback = $this->argument(4)->type('closure')->value();

        $ellipse_classname = sprintf('\YiiMan\LibUploadManager\lib\imageManager\%s\Shapes\EllipseShape',
            $image->getDriver()->getDriverName());

        $ellipse = new $ellipse_classname($width, $height);

        if ($callback instanceof Closure) {
            $callback($ellipse);
        }

        $ellipse->applyToImage($image, $x, $y);

        return true;
    }
}
