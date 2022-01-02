<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Imagick\Commands;

class InterlaceCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Toggles interlaced encoding mode
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $mode = $this->argument(0)->type('bool')->value(true);

        if ($mode) {
            $mode = \Imagick::INTERLACE_LINE;
        } else {
            $mode = \Imagick::INTERLACE_NO;
        }

        $image->getCore()->setInterlaceScheme($mode);

        return true;
    }
}
