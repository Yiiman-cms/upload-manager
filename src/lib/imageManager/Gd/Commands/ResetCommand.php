<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Gd\Commands;

class ResetCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Resets given image to its backup state
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $backupName = $this->argument(0)->value();

        if (is_resource($backup = $image->getBackup($backupName))) {

            // destroy current resource
            imagedestroy($image->getCore());

            // clone backup
            $backup = $image->getDriver()->cloneCore($backup);

            // reset to new resource
            $image->setCore($backup);

            return true;
        }

        throw new \YiiMan\LibUploadManager\lib\imageManager\Exception\RuntimeException(
            "Backup not available. Call backup() before reset()."
        );
    }
}
