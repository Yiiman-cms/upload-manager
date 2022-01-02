<?php

namespace YiiMan\LibUploadManager\lib\imageManager\Filters;

interface FilterInterface
{
    /**
     * Applies filter to given image
     *
     * @param  \YiiMan\LibUploadManager\lib\imageManager\Image $image
     * @return \YiiMan\LibUploadManager\lib\imageManager\Image
     */
    public function applyFilter(\YiiMan\LibUploadManager\lib\imageManager\Image $image);
}
