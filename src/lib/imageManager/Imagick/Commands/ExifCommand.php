<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Imagick\Commands;

use YiiMan\LibUploadManager\lib\imageManager\Commands\ExifCommand as BaseCommand;

class ExifCommand extends BaseCommand
{
    /**
     * Prefer extension or not
     *
     * @var bool
     */
    private $preferExtension = true;

    /**
     *
     */
    public function dontPreferExtension() {
        $this->preferExtension = false;
    }

    /**
     * Read Exif data from the given image
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        if ($this->preferExtension && function_exists('exif_read_data')) {
            return parent::execute($image);
        }

        $core = $image->getCore();

        if ( ! method_exists($core, 'getImageProperties')) {
            throw new \YiiMan\LibUploadManager\lib\imageManager\Exception\NotSupportedException(
                "Reading Exif data is not supported by this PHP installation."
            );
        }

        $requestedKey = $this->argument(0)->value();
        if ($requestedKey !== null) {
            $this->setOutput($core->getImageProperty('exif:' . $requestedKey));
            return true;
        }

        $exif = [];
        $properties = $core->getImageProperties();
        foreach ($properties as $key => $value) {
            if (substr($key, 0, 5) !== 'exif:') {
                continue;
            }

            $exif[substr($key, 5)] = $value;
        }

        $this->setOutput($exif);
        return true;
    }
}
