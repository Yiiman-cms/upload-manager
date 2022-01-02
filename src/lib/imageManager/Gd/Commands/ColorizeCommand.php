<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Gd\Commands;

class ColorizeCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Changes balance of different RGB color channels
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $red = $this->argument(0)->between(-100, 100)->required()->value();
        $green = $this->argument(1)->between(-100, 100)->required()->value();
        $blue = $this->argument(2)->between(-100, 100)->required()->value();

        // normalize colorize levels
        $red = round($red * 2.55);
        $green = round($green * 2.55);
        $blue = round($blue * 2.55);

        // apply filter
        return imagefilter($image->getCore(), IMG_FILTER_COLORIZE, $red, $green, $blue);
    }
}
