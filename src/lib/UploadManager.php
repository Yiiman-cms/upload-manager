<?php


namespace YiiMan\LibUploadManager\lib;

use BadMethodCallException;
use Exception;
use InvalidArgumentException;
use yii\web\HttpException;
use YiiMan\LibUploadManager\module\models\DynamicModel;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use YiiMan\Setting\module\components\Options;
use function realpath;

/**
 * Class UploadManager
 * @package YiiMan\LibUploadManager
 */
class UploadManager
{
    public $dir;
    public $model;
    public $attribute;
    public $errors = [];
    private $uploadDir;
    public $options;
    private $imageManager;
    public function __construct()
    {

        $this->imageManager=new ImageManager();
        $this->uploadDir = Yii::$app->Options->UploadDir . $this->dir;
    }

    /**
     * @param ActiveRecord|DynamicModel|\yii\base\DynamicModel $model not empty model
     * @param string $attribute your file attribute
     *
     * @return string sile name
     * @throws \yii\base\InvalidConfigException
     */
    public function save($model, $attribute)
    {
        /**
         * @var $model
         */
        if (!empty($_FILES)) {
            if (empty($this->image)) {
                $files = UploadedFile::getInstance($model, $attribute);
               
                $fileName = uniqid() . '.' . $files->extension;
                $dir = Yii::$app->Options->UploadDir . '/' . $this->getUploadDirectory(
                        $model
                    ) . '/' . $fileName;
                $this->MakeDir(Yii::$app->Options->UploadDir . '/' . $this->getUploadDirectory($model));
                if (!empty($this->errors)) {
                    echo '<pre style="direction:ltr">';
                    var_dump($this->errors);
                    die();

                }
                try{
                    $files->saveAs($dir);
                }catch (Exception $e){
                    throw new HttpException(  \Yii::t('site','لطفا تنظیمات پوشه ی آپلود را بررسی کنید') );
                }

                return $fileName;
            }
        }
    }

    /**
     * this will return folder that file will be upload on that
     *
     * @param \yii\db\ActiveRecord $model
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getUploadDirectory($model)
    {
        return $dir = $model->formName() . '/' . $model->id;
    }

    private function MakeDir($dir, $fullPath = true)
    {
        $exist = realpath($dir);


        if (empty($exist)) {
            $chunkedDir = explode('/', $dir);
            if (count($chunkedDir) > 1 && $fullPath) {
                $mkDirectory = '';
                foreach ($chunkedDir as $item) {
                    if (!empty($mkDirectory)) {
                        $mkDirectory .= '/' . $item;
                    } else {
                        $mkDirectory = $item;
                    }
                    $this->MakeDir($mkDirectory, false);
                }
            } else if ($fullPath) {
                throw new BadMethodCallException(
                    'Your Upload Directory Settings Is Invalid, Please Set On Settings Menu'
                );
            } else {
                $mkDir = true;
                if (PHP_OS == 'Linux') {
                    if (empty(realpath('/' . $dir))) {


                        $mkDir = mkdir('/' . $dir, 0775);
                        chmod('/' . $dir, 0755);
                    }
                } else {
                    if (empty(realpath($dir))) {
                        $mkDir = mkdir($dir, 0755);
                        if (PHP_OS == 'Linux') {

                            chmod('/' . $dir, 0755);
                        } else {
                            chmod($dir, 0755);
                        }
                    }

                }
                if (!$mkDir) {
                    $this->errors[] = ['error in make directory', 'directory' => $dir];
                }
            }
        }
    }

    private function addError($key, $content)
    {
        $this->errors[] =
            [
                'title' => $key,
                'content' => $content
            ];
    }


    public function getImageUrl($model, $attrName, $size = 'default')
    {
        /* < Variables > */
        {
            $attribute = $model->$attrName;

            $options = Yii::$app->Options;
            $uploadUrl = $options->UploadUrl;
            $uploadDir = $options->UploadDir;
        }
        /* </ Variables > */

        /* < calculate image index > */
        {

            $image = $attribute;

        }
        /* </ calculate image index > */

        /* < check validate $image variable > */
        {
            if (empty($image)) {
                return $this->generateNoImage($size);
            }
        }
        /* </ check validate $image variable > */


        /* < check image is exist in public upload folder? > */
        {
            $uploadPath = Yii::$app->Options->UploadDir . '/' . $this->getUploadDirectory($model);
            $exist = realpath($uploadPath);
            if ($exist) {

                /* < Check Generation Size > */
                {
                    $url = $uploadUrl . '/' . $this->getUploadDirectory($model) . '/' . $image;
                    $dir = $uploadDir . '/' . $this->getUploadDirectory($model);
                    if (!empty($size)) {
                        if ($size == 'default') {
                            return $url;
                        } else {
                            $sizeText = $size;
                            $size = str_replace(['*', ' ', '.', ','], '*', $size);
                            $size = explode('*', $size);
                            if (!empty($size)) {
                                /* < Split Size > */
                                {
                                    $width = $size[0];
                                    $heiht = $size[1];
                                }
                                /* </ Split Size > */

                                /* < Goto Private Upload Directory > */
                                {
                                    /* < parse image name > */
                                    {
                                        $folderAddress = $dir . '/' . $width . '_' . $heiht;
                                        $fileAddress = $uploadUrl . '/' . $this->getUploadDirectory(
                                                $model
                                            ) . '/' . $width . '_' . $heiht . '/' . $image;

                                        /* < Image name folder extraction > */
                                        {
                                            if (!realpath($folderAddress)) {
                                                @mkdir($folderAddress);
                                            }

                                        }
                                        /* </ Image name folder extraction > */


                                        if (!realpath($fileAddress)) {
                                            $dir = str_replace('\\', '/', $dir);

                                            $this->imageManager->make($dir . '/' . $image)->resize(
                                                $width,
                                                $heiht
                                            )->save($dir . '/' . $width . '_' . $heiht . '/' . $image, 100);
                                        }

//
                                        return $uploadUrl . '/' . $this->getUploadDirectory(
                                                $model
                                            ) . '/' . $width . '_' . $heiht . '/' . $image;
                                    }
                                    /* </ parse image name > */
                                }
                                /* </ Goto Private Upload Directory > */

                            } else {
                                return $url;
                            }
                        }
                    } else {
                        return $url;
                    }


                }
                /* </ Check Generation Size > */
            } else {
                return $this->generateNoImage($size);
            }
        }
        /* </ check image is exist in public upload folder? > */

    }

    /**
     * @param string $path file location from upload folder sample : works/image
     * @param string $size
     * @param string $fileName
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getResizedUrl($path, $fileName, $size = 'default')
    {
        $options = Yii::$app->Options;
        $uploadUrl = $options->UploadUrl;
        $uploadDir = $options->UploadDir;
        /* < Check Generation Size > */
        {
            $url = $uploadUrl . '/' . $path . '/' . $fileName;
            $dir = $uploadDir . '/' . $path;
            if (!empty($size)) {
                if ($size == 'default') {
                    return $url;
                } else {

                    $size = str_replace(['*', ' ', '.', ','], '*', $size);
                    $size = explode('*', $size);
                    if (!empty($size)) {
                        /* < Split Size > */
                        {
                            $width = $size[0];
                            $heiht = $size[1];
                        }
                        /* </ Split Size > */

                        /* < Goto Private Upload Directory > */
                        {
                            /* < parse image name > */
                            {
                                $folderAddress = $dir . '/' . $width . '_' . $heiht;
                                $fileAddress = $dir . '/' . $width . '_' . $heiht . '/' . $fileName;

                                /* < Image name folder extraction > */
                                {
                                    if (!realpath($folderAddress)) {
                                        @mkdir($folderAddress);
                                    }

                                }
                                /* </ Image name folder extraction > */


                                if (!realpath($fileAddress)) {
                                    $dir = str_replace('\\', '/', $dir);
                                    if (realpath($dir . '/' . $fileName)) {
                                        $this->imageManager->make($dir . '/' . $fileName)->resize(
                                            $width,
                                            $heiht
                                        )->save($dir . '/' . $width . '_' . $heiht . '/' . $fileName, 100);
                                        return $uploadUrl . '/' . $path . '/' . $width . '_' . $heiht . '/' . $fileName;

                                    }else{
                                        return $this->generateNoImage($width.'*'.$heiht);
                                    }
                                }

//
                            }
                            /* </ parse image name > */
                        }
                        /* </ Goto Private Upload Directory > */

                    } else {
                        return $url;
                    }
                }
            } else {
                return $url;
            }


        }
        /* </ Check Generation Size > */
    }

    /**
     * @param string $path file location from upload folder sample : works/image
     * @param string $size
     * @param string $fileName
     * @param string $background
     * @param string $align center,top,right,bottom,left
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getResizedCanvasUrl($path, $fileName, $align, $size = 'default', $background)
    {
        $options = Yii::$app->Options;
        $uploadUrl = $options->UploadUrl;
        $uploadDir = $options->UploadDir;
        /* < Check Generation Size > */
        {
            $url = $uploadUrl . '/' . $path . '/' . $fileName;
            $dir = $uploadDir . '/' . $path;
            if (!empty($size)) {
                if ($size == 'default') {
                    return $url;
                } else {

                    $size = str_replace(['*', ' ', '.', ','], '*', $size);
                    $size = explode('*', $size);
                    if (!empty($size)) {
                        /* < Split Size > */
                        {
                            $width = $size[0];
                            $heiht = $size[1];
                        }
                        /* </ Split Size > */

                        /* < Goto Private Upload Directory > */
                        {
                            /* < parse image name > */
                            {
                                $folderAddress = $dir . '/resizedCanvas/' . $width . '_' . $heiht;
                                $fileAddress = $dir . '/resizedCanvas/' . $width . '_' . $heiht . '/' . $fileName;

                                /* < Image name folder extraction > */
                                {

                                    if (!realpath(str_replace(['/', '\\', '\\\\'], DIRECTORY_SEPARATOR, $folderAddress))) {
                                        @mkdir(str_replace(['/', '\\', '\\\\'], DIRECTORY_SEPARATOR, $folderAddress), 0777, true);
                                    }

                                }
                                /* </ Image name folder extraction > */


                                if (!realpath($fileAddress)) {
                                    $dir = str_replace('\\', '/', $dir);
                                    if (realpath($dir . '/' . $fileName)) {
                                        $this->imageManager->make($dir . '/' . $fileName)->resizeCanvas(
                                            $width,
                                            $heiht,
                                            $align,
                                            false,
                                            $background
                                        )->save($dir . '/resizedCanvas/' . $width . '_' . $heiht . '/' . $fileName, 100);
                                        return $uploadUrl . '/' . $path . '/resizedCanvas/' . $width . '_' . $heiht . '/' . $fileName;

                                    }else{
                                        return $this->generateNoImage($width.'*'.$heiht);
                                    }
                                }

//
                            }
                            /* </ parse image name > */
                        }
                        /* </ Goto Private Upload Directory > */

                    } else {
                        return $url;
                    }
                }
            } else {
                return $url;
            }


        }
        /* </ Check Generation Size > */
    }

    /**
     * @param string $path file location from upload folder sample : works/image
     * @param string $size
     * @param string $fileName
     * @param string $cropAxis
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getResizedCroped($path, $fileName, $size, $cropAxis)
    {
        $options = Yii::$app->Options;
        $uploadUrl = $options->UploadUrl;
        $uploadDir = $options->UploadDir;
        /* < Check Generation Size > */
        {
            $url = $uploadUrl . '/' . $path . '/' . $fileName;
            $dir = $uploadDir . '/' . $path;
            if (!empty($size)) {
                if ($size == 'default') {
                    return $url;
                } else {

                    $size = str_replace(['*', ' ', '.', ','], '*', $size);
                    $size = explode('*', $size);

                    $cropAxis = str_replace(['*', ' ', '.', ','], '*', $cropAxis);
                    $cropAxis = explode('*', $cropAxis);


                    if (!empty($size)) {
                        /* < Split Size > */
                        {
                            $width = $size[0];
                            $heiht = $size[1];

                            $x = $cropAxis[0];
                            $y = $cropAxis[1];


                        }
                        /* </ Split Size > */

                        /* < Goto Private Upload Directory > */
                        {
                            /* < parse image name > */
                            {
                                $folderAddress = $dir . '/croped/' . $width . '_' . $heiht;
                                $fileAddress = $dir . '/croped/' . $width . '_' . $heiht . '/' . $fileName;

                                /* < Image name folder extraction > */
                                {

                                    if (!realpath(str_replace(['/', '\\', '\\\\'], DIRECTORY_SEPARATOR, $folderAddress))) {
                                        @mkdir(str_replace(['/', '\\', '\\\\'], DIRECTORY_SEPARATOR, $folderAddress), 0777, true);
                                    }

                                }
                                /* </ Image name folder extraction > */


                                if (!realpath($fileAddress)) {
                                    $dir = str_replace('\\', '/', $dir);
                                    if (realpath($dir . '/' . $fileName)) {
                                        $this->imageManager->make($dir . '/' . $fileName)->crop(
                                            $width,
                                            $heiht,
                                            $x,
                                            $y
                                        )->save($dir . '/croped/' . $width . '_' . $heiht . '/' . $fileName, 100);

                                        return $uploadUrl . '/' . $path . '/croped/' . $width . '_' . $heiht . '/' . $fileName;

                                    }else{
                                        return $this->generateNoImage($width . '*' . $heiht);
                                    }
                                }

//
                            }
                            /* </ parse image name > */
                        }
                        /* </ Goto Private Upload Directory > */

                    } else {
                        return $url;
                    }
                }
            } else {
                return $url;
            }


        }
        /* </ Check Generation Size > */
    }

    /**
     * @param string $path file location from upload folder sample : works/image
     * @param string $size
     * @param string $fileName
     * @param string $align center,top,right,bottom,left
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getFit($path, $fileName, $size, $align = 'center')
    {
        $orgSize=$size;
        $options = Yii::$app->Options;
        $uploadUrl = Yii::$app->Options->URL.$options->UploadUrl;
        $uploadDir = $options->UploadDir;
        $folderName = DIRECTORY_SEPARATOR . 'fit' . DIRECTORY_SEPARATOR;
        /* < Check Generation Size > */
        {
            $url = $uploadUrl . '/' . $path . '/' . $fileName;
            $dir = $uploadDir . '/' . $path;
            if (!empty($size)) {
                if ($size == 'default') {
                    return $url;
                } else {

                    $size = str_replace(['*', ' ', '.', ','], '*', $size);
                    $size = explode('*', $size);


                    if (!empty($size)) {
                        /* < Split Size > */
                        {
                            $width = $size[0];
                            $heiht = $size[1];


                        }
                        /* </ Split Size > */

                        /* < Goto Private Upload Directory > */
                        {
                            /* < parse image name > */
                            {
                                $folderAddress = $dir . $folderName . $width . '_' . $heiht;
                                $fileAddress = $dir . $folderName . $width . '_' . $heiht . '/' . $fileName;

                                /* < Image name folder extraction > */
                                {

                                    if (!realpath(str_replace(['/', '\\', '\\\\'], DIRECTORY_SEPARATOR, $folderAddress))) {
                                        @mkdir(str_replace(['/', '\\', '\\\\'], DIRECTORY_SEPARATOR, $folderAddress), 0777, true);
                                    }

                                }
                                /* </ Image name folder extraction > */


                                if (!realpath($fileAddress)) {
                                    $dir = str_replace('\\', '/', $dir);


                                    if (!realpath($dir . '/' . $fileName)){
                                        return $this->generateNoImage($orgSize);
                                    }
                                    $this->imageManager->make($dir . '/' . $fileName)->fit(
                                        $width,
                                        $heiht,
                                        null,
                                        $align
                                    )->save($dir . $folderName . $width . '_' . $heiht . '/' . $fileName, 100);
                                }

//
                                $url= $uploadUrl . '/' . $path . $folderName . $width . '_' . $heiht . '/' . $fileName;
                                return str_replace('\\','/',$url);
                            }
                            /* </ parse image name > */
                        }
                        /* </ Goto Private Upload Directory > */

                    } else {
                        return str_replace('\\','/',$url);;
                    }
                }
            } else {
                return str_replace('\\','/',$url);;
            }


        }
        /* </ Check Generation Size > */
    }


    private function dowunloadFile($url, $dir, $urlFileName)
    {
        $options = Yii::$app->Options;
        $uploadUrl = $options->UploadUrl;
        $uploadDir = $options->UploadDir;
        $fileContent = file_get_contents($url);
        if (!realpath($uploadDir . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR)) {
            @mkdir($uploadDir . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR, 0777, true);
        }
        $file = fopen($uploadDir . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $urlFileName, 'w+');
        fwrite($file, $fileContent);
        fclose($file);
        return $urlFileName;
    }

    /**
     * @param string $path file location from upload folder sample : works/image
     * @param string $size
     * @param string $fileName
     * @param string $align center,top,right,bottom,left
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getFitFromUrl($url, $path, $size, $align = 'center')
    {

        $options = Yii::$app->Options;
        $uploadUrl = $options->UploadUrl;
        $uploadDir = $options->UploadDir;
        $folderName = DIRECTORY_SEPARATOR . 'fit' . DIRECTORY_SEPARATOR;

        $explodes = explode('/', $url);
        $urlFileName = $explodes[count($explodes) - 1];
        if (!realpath($uploadDir . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $urlFileName)) {
            $this->dowunloadFile($url, $path, $urlFileName);
        }

        /* < Check Generation Size > */
        {
            $url = $uploadUrl . '/' . $path . '/' . $urlFileName;
            $dir = $uploadDir . '/' . $path;
            if (!empty($size)) {
                if ($size == 'default') {
                    return $url;
                } else {

                    $size = str_replace(['*', ' ', '.', ','], '*', $size);
                    $size = explode('*', $size);


                    if (!empty($size)) {
                        /* < Split Size > */
                        {
                            $width = $size[0];
                            $heiht = $size[1];


                        }
                        /* </ Split Size > */

                        /* < Goto Private Upload Directory > */
                        {
                            /* < parse image name > */
                            {
                                $folderAddress = $dir . $folderName . $width . '_' . $heiht;
                                $fileAddress = $dir . $folderName . $width . '_' . $heiht . '/' . $urlFileName;

                                /* < Image name folder extraction > */
                                {

                                    if (!realpath(str_replace(['/', '\\', '\\\\'], DIRECTORY_SEPARATOR, $folderAddress))) {
                                        @mkdir(str_replace(['/', '\\', '\\\\'], DIRECTORY_SEPARATOR, $folderAddress), 0777, true);
                                    }

                                }
                                /* </ Image name folder extraction > */


                                if (!realpath($fileAddress)) {
                                    $dir = str_replace('\\', '/', $dir);

                                    $this->imageManager->make($dir . '/' . $urlFileName)->fit(
                                        $width,
                                        $heiht,
                                        null,
                                        $align
                                    )->save($dir . $folderName . $width . '_' . $heiht . '/' . $urlFileName, 100);
                                }

//
                                return $uploadUrl . '/' . $path . $folderName . $width . '_' . $heiht . '/' . $urlFileName;
                            }
                            /* </ parse image name > */
                        }
                        /* </ Goto Private Upload Directory > */

                    } else {
                        return $url;
                    }
                }
            } else {
                return $url;
            }


        }
        /* </ Check Generation Size > */
    }

public function changeResolution($fileLocation, $dpi)
    {
        $options = Yii::$app->Options;
        $uploadUrl = $options->UploadUrl;
        $uploadDir = $options->UploadDir;
        $fileLocation=str_replace('\\','/',$fileLocation);
        $explodedPath=explode('/',$fileLocation);
        $fileName=$explodedPath[(count($explodedPath)-1)];
        $filePath='';
        foreach ($explodedPath as $p){
            if (empty($p)){

                continue;
            }
            if ($p==$fileName){
                continue;
            }
            $filePath .='/'.$p;
        }

        /* < Check Generation Size > */
        {
            $url = str_replace($uploadDir,$uploadUrl ,$fileLocation)  ;
            $url=str_replace('\\','/',$url);




            /* < Goto Private Upload Directory > */
            {
                /* < parse image name > */
                {
                    $folderAddress = $filePath .'/' . $dpi.'DPI';

                    /* < Image name folder extraction > */
                    {

                        if (!realpath(str_replace(['/', '\\', '\\\\'], DIRECTORY_SEPARATOR, $folderAddress))) {
                            @mkdir(str_replace(['/', '\\', '\\\\'], DIRECTORY_SEPARATOR, $folderAddress), 0777, true);
                        }

                    }
                    /* </ Image name folder extraction > */


                    if (!realpath($folderAddress.'/'.$fileName)) {
                        copy($filePath .'/'.$fileName,$folderAddress.'/'.$fileName);
                        $image=imagecreatefromjpeg($folderAddress.'/'.$fileName);
                        imageresolution($image, $dpi, $dpi);
                    }

                    $out= str_replace($_ENV['YII_PUBLIC_HTML_DIR'],'',realpath($folderAddress.'/'.$fileName));
                   return $out;
                }
                /* </ parse image name > */
            }
            /* </ Goto Private Upload Directory > */


        }
        /* </ Check Generation Size > */
    }


    private function generateNoImage($size = 'default', $image = 'noImage.jpg')
    {
        $uploadPath = Yii::$app->Options->UploadDir . '/images/noImage.jpg';
        $exist = realpath(trim($uploadPath));
        if ($exist) {

            /* < Check Generation Size > */
            {
                $url = Yii::$app->Options->UploadUrl . '/images/noImage.jpg';
                $dir = Yii::$app->Options->UploadDir . '/images/noImage.jpg';
                if (!empty($size)) {
                    if ($size == 'default') {
                        return $url;
                    } else {
                        $size = str_replace(['*', ' ', '.', ','], '*', $size);
                        $size = explode('*', $size);
                        if (!empty($size)) {
                            /* < Split Size > */
                            {
                                $width = $size[0];
                                $heiht = $size[1];
                            }
                            /* </ Split Size > */

                            /* < Goto Private Upload Directory > */
                            {
                                /* < parse image name > */
                                {
                                    $folderAddress = Yii::$app->Options->UploadDir . '/images/' . $width . '_' . $heiht;
                                    $fileAddress = $folderAddress . '/' . $image;

                                    /* < Image name folder extraction > */
                                    {
                                        if (!realpath($folderAddress)) {
                                            @mkdir($folderAddress);
                                        }

                                    }
                                    /* </ Image name folder extraction > */


                                    if (!realpath($fileAddress)) {

                                        $this->imageManager->make($dir)->resize(
                                            $width,
                                            $heiht
                                        )->save($fileAddress, 100);
                                    }

//
                                    return Yii::$app->Options->URL . '/' . Yii::$app->Options->UploadUrl . '/images/' . $width . '_' . $heiht . '/' . $image;
                                }
                                /* </ parse image name > */
                            }
                            /* </ Goto Private Upload Directory > */

                        } else {
                            return $url;
                        }
                    }
                } else {
                    return $url;
                }


            }
            /* </ Check Generation Size > */
        } else {
            $this->MakeDefaultPic(Yii::$app->Options->UploadDir . '/images');
            $this->generateNoImage($size, $image);
        }
    }

    public function checkIndex(array $array, int $index)
    {
        if (!empty($array[$index])) {
            return $array[$index];
        } else {
            return $this->checkIndex($array, ((integer)$index - 1));
        }
    }

    public function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    private function MakeDefaultPic($dir)
    {
        $dir=str_replace(['/','\\'],DIRECTORY_SEPARATOR,$dir);

        if (!realpath($dir)) {
            @mkdir($dir, true);
        }
        $noImage=trim($dir .DIRECTORY_SEPARATOR. 'noImage.jpg');
        if (!file_exists($noImage)){
            $file = fopen($noImage, 'w+');

            fwrite($file, file_get_contents(__DIR__ . '/no-image.jpg'));
            fclose($file);
        }
    }

    public function mimeToExtensions($mime=[],$rebuild=false)
    {
        if ($rebuild){
            $extensions = file_get_contents(__DIR__ . '/mime.json');
            $extensions = json_decode($extensions);
            $types = [];

            foreach ($extensions as $ex => $mime) {
                $type = explode('/', $mime)[0];
                if (empty($types[$type])) {
                    $types[$type] = [$ex];
                } else {
                    $types[$type][] = $ex;
                }
            }
            $file = fopen(__DIR__ . '/mime-types.json', 'w+');
            fwrite($file, json_encode($types));
            fclose($file);
        }

        $mimeArea= json_decode(file_get_contents(__DIR__.'/mime-types.json'),true);
        $out=[];
        foreach ($mime as $m){
            $out=ArrayHelper::merge($out,$mimeArea[$m]);
        }
        return $out;
    }

    public function extensionsToMime($extension)
    {
        $extension=strtolower($extension);
        $mimeArea= json_decode(file_get_contents(__DIR__.'/mime.json'),true);

        $mime= $mimeArea[str_replace('.','',$extension)];
        $mime=explode('/',$mime);
        return $mime[0];
    }


}
