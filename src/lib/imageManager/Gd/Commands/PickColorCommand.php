<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Gd\Commands;

use YiiMan\LibUploadManager\lib\imageManager\Gd\Color;

class PickColorCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Read color information from a certain position
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $x = $this->argument(0)->type('digit')->required()->value();
        $y = $this->argument(1)->type('digit')->required()->value();
        $format = $this->argument(2)->type('string')->value('array');

        // pick color
        $color = imagecolorat($image->getCore(), $x, $y);

        if ( ! imageistruecolor($image->getCore())) {
            $color = imagecolorsforindex($image->getCore(), $color);
            $color['alpha'] = round(1 - $color['alpha'] / 127, 2);
        }

        $color = new Color($color);

        // format to output
        $this->setOutput($color->format($format));

        return true;
    }
}
