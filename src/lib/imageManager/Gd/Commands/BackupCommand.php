<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Gd\Commands;

class BackupCommand extends \YiiMan\LibUploadManager\lib\imageManager\Commands\AbstractCommand
{
    /**
     * Saves a backups of current state of image core
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $backupName = $this->argument(0)->value();

        // clone current image resource
        $clone = clone $image;
        $image->setBackup($clone->getCore(), $backupName);

        return true;
    }
}
