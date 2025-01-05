<?php

namespace src\helpers;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use src\App;

/**
 * @author Tim Zapfe
 */
class FileHelper extends BaseHelper
{
    public static array $IMAGE_HANDLERS = [
        IMAGETYPE_JPEG => [
            'load'    => 'imagecreatefromjpeg',
            'save'    => 'imagejpeg',
            'quality' => 100
        ],
        IMAGETYPE_PNG  => [
            'load'    => 'imagecreatefrompng',
            'save'    => 'imagepng',
            'quality' => 0
        ],
        IMAGETYPE_GIF  => [
            'load' => 'imagecreatefromgif',
            'save' => 'imagegif'
        ]
    ];

    /**
     * Returns boolean whether a file exists or not.
     * @param string $path + BASE_PATH will be added before.
     * @return bool
     * @author Tim Zapfe
     */
    public static function exist(string $path): bool
    {
        return file_exists(BASE_PATH . $path);
    }

    /**
     * Returns the given path with BASE_PATH before.
     * @param string $path
     * @return string
     * @author Tim Zapfe
     */
    public static function get(string $path): string
    {
        return BASE_PATH . $path;
    }

    /**
     * Returns the content of a file if exists.
     * @param string $path
     * @return string|bool|null
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public static function getFileContent(string $path): string|null|bool
    {
        if (self::exist($path)) {
            return file_get_contents(self::get($path));
        }

        return null;
    }

    /**
     * Returns the content of a file which was included.
     * @param string $path
     * @return mixed|null
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public static function getFileInclude(string $path): mixed
    {
        if (self::exist($path)) {
            return include(self::get($path));
        }

        return null;
    }


    /**
     * Returns array of files inside a given folder.
     * @param string $path
     * @return array
     * @author Tim Zapfe
     * @date 07.11.2024
     */
    public static function getFilesInFolder(string $path): array
    {
        $path = self::get($path);

        // Check if the path is a directory
        if (!is_dir($path)) {
            return [];
        }

        // Use scandir to get an array of files and directories
        $files = scandir($path);

        // Filter out "." and ".." entries and keep only files
        $files = array_filter($files, function ($file) use ($path) {
            return is_file($path . DIRECTORY_SEPARATOR . $file);
        });

        // Return the array of files
        return array_values($files); // Reset array keys
    }

    /**
     * Returns the src of an image. When height or width is specified in config, it will create an image with the right proportions.
     * @param string $src
     * @param string|null $type
     * @param array $config
     * @return string
     * @author Tim Zapfe
     */
    public static function getImage(string $src, string $type = null, array $config = []): string
    {
        $info = pathinfo($src);
        $filename = $info['filename'] ?? 'default';
        $extension = '.' . ($info['extension'] ?? 'png');

        // check if height is given by config
        $filename .= (!empty($config['height']) && $config['resize']) ? '_h' . $config['height'] : '';
        $filename .= (!empty($config['width']) && $config['resize']) ? '_w' . $config['width'] : '';

        // any height or width given?
        if ((!empty($config['height']) || !empty($config['width'])) && $config['resize']) {
            $filename .= '_cached';
        }

        $filename .= $extension;

        $folders = App::getConfig()->assetImageFolders;
        $folder = !empty($folders[$type]) ? $folders[$type] : null;

        // set paths
        $webPath = BaseHelper::getUrl() . 'assets/images/';
        $serverPath = DS . 'web' . DS . 'assets' . DS . 'images' . DS;

        // check if folder exists
        if (!empty($folder) && self::exist($serverPath . $folder)) {
            // add folder
            $webPath .= $folder . '/';
            $serverPath .= $folder . DS;
        }

        // check if base file exists
        if (self::exist($serverPath . $src)) {

            // check if with height and width exists
            if (self::exist($serverPath . $filename)) {

                // return it
                return $webPath . $filename;
            } else {
                // create it with dimensions
                $fullPath = self::get($serverPath);
                $thumbnailCreated = self::_createImageWithConfig($fullPath . $src,
                    $fullPath . $filename,
                    $config['width'] ?? null,
                    $config['height'] ?? null
                );

                if (!empty($thumbnailCreated)) {
                    return $webPath . $filename;
                }
            }
        }

        // check if default.png exists in folder
        if (self::exist($serverPath . 'default.png')) {
            // return default.png
            return $webPath . 'default.png';
        }

        // return the main default
        return BaseHelper::getUrl() . 'assets/images/default.png';
    }

    /**
     * Makes an image bigger/smaller with height/width dimesions.
     * @param $src
     * @param $dest
     * @param $targetWidth
     * @param $targetHeight
     * @return false|mixed
     * @author Tim Zapfe
     */
    private static function _createImageWithConfig($src, $dest, $targetWidth = null, $targetHeight = null): mixed
    {


        // 1. Load the image from the given $src
        // - see if the file actually exists
        // - check if it's of a valid image type
        // - load the image resource

        // get the type of the image
        // we need the type to determine the correct loader
        $type = exif_imagetype($src);

        // if no valid type or no handler found -> exit
        if (!$type || !self::$IMAGE_HANDLERS[$type]) {
            return false;
        }

        // load the image with the correct loader
        $image = call_user_func(self::$IMAGE_HANDLERS[$type]['load'], $src);

        // no image found at supplied location -> exit
        if (!$image) {
            return false;
        }

        // 2. Create a thumbnail and resize the loaded $image
        // - get the image dimensions
        // - define the output size appropriately
        // - create a thumbnail based on that size
        // - set alpha transparency for GIFs and PNGs
        // - draw the final thumbnail

        // get original image width and height
        $width = imagesx($image);
        $height = imagesy($image);
        $ratio = $width / $height;

        // check if both width and height are null?
        if (empty($targetHeight) && empty($targetWidth)) {
            return false;
        }

        // maintain aspect ratio when no height set
        if (empty($targetHeight)) {
            // if is portrait
            // use ratio to scale height to fit in square
            if ($width > $height) {
                $targetHeight = floor($targetWidth / $ratio);
            }
            // if is landscape
            // use ratio to scale width to fit in square
            else {
                $targetHeight = $targetWidth;
                $targetWidth = floor($targetWidth * $ratio);
            }
        }

        // maintain aspect ratio when no width set
        if (empty($targetWidth)) {
            $targetWidth = floor($targetHeight * $ratio);
        }

        // create duplicate image based on calculated target size
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        // set transparency options for GIFs and PNGs
        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {

            // make image transparent
            imagecolortransparent(
                $thumbnail,
                imagecolorallocate($thumbnail, 0, 0, 0)
            );

            // additional settings for PNGs
            if ($type == IMAGETYPE_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
        }

        // copy entire source image to duplicate image and resize
        imagecopyresampled(
            $thumbnail,
            $image,
            0, 0, 0, 0,
            $targetWidth, $targetHeight,
            $width, $height
        );

        // 3. Save the $thumbnail to disk
        // - call the correct save method
        // - set the correct quality level

        // save the duplicate version of the image to disk
        return call_user_func(
            self::$IMAGE_HANDLERS[$type]['save'],
            $thumbnail,
            $dest,
            self::$IMAGE_HANDLERS[$type]['quality']
        );
    }

    /**
     * Delete all cached images
     * @param string|null $containsName
     * @return void
     * @author Tim Zapfe
     */
    public static function clearCachedImages(string $containsName = null): void
    {
        // only work when environment is dev
        if ($_ENV['ENVIRONMENT'] === 'dev') {
            {
                $dir = ASSET_PATH . DS . 'images';

                // Initialize Directory Iterator
                $directory = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
                $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);

                // loop through all files
                foreach ($iterator as $file) {

                    // Check if the file name contains "cached" and is an image
                    if ($file->isFile() && str_contains($file->getFilename(), 'cached') && preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $file->getFilename())) {

                        // does specific name exist?
                        if (!empty($containsName)) {
                            // does name contains searched string?
                            if (str_contains($file->getFilename(), $containsName)) {
                                // delete file
                                unlink($file->getRealPath());
                            }

                            // continue...
                        } else {
                            // delete all cached
                            unlink($file->getRealPath());
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns boolean whether the file exists as a page or content in the templates.
     * @param string $file
     * @return bool
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    public static function existsAsPageOrContentInTemplates(string $file): bool
    {
        $templatePath = App::getAlias('templatePath');

        // as page?
        if (FileHelper::exist($templatePath . DS . 'pages' . DS . $file . '.twig')) {
            return true;
        }

        // as content?
        if (FileHelper::exist($templatePath . DS . 'content' . DS . $file . DS . 'entry.twig')) {
            return true;
        }

        return false;
    }
}