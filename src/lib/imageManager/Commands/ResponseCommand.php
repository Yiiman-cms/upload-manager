<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Commands;

use YiiMan\LibUploadManager\lib\imageManager\Response;

class ResponseCommand extends AbstractCommand
{
    /**
     * Builds HTTP response from given image
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $format = $this->argument(0)->value();
        $quality = $this->argument(1)->between(0, 100)->value();

        $response = new Response($image, $format, $quality);

        $this->setOutput($response->make());

        return true;
    }
}
