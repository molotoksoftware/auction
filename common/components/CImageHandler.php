<?php

/**
 * Image handler
 * @author  Yaroslav Pelesh aka Tokolist http://tokolist.com
 * @link    https://github.com/tokolist/yii-components
 * @version 1.2
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class CImageHandler extends CApplicationComponent
{
    private $originalImage = null;
    private $image = null;

    private $format = 0;

    private $width = 0;
    private $height = 0;

    private $mimeType = '';

    private $fileName = '';

    public $transparencyColor = [0, 0, 0];


    const IMG_GIF = 1;
    const IMG_JPEG = 2;
    const IMG_PNG = 3;

    const CORNER_LEFT_TOP = 1;
    const CORNER_RIGHT_TOP = 2;
    const CORNER_LEFT_BOTTOM = 3;
    const CORNER_RIGHT_BOTTOM = 4;
    const CORNER_CENTER = 5;
    const CORNER_CENTER_TOP = 6;
    const CORNER_CENTER_BOTTOM = 7;
    const CORNER_LEFT_CENTER = 8;
    const CORNER_RIGHT_CENTER = 9;

    const FLIP_HORIZONTAL = 1;
    const FLIP_VERTICAL = 2;
    const FLIP_BOTH = 3;


    public function getImage()
    {
        return $this->image;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function __destruct()
    {
        $this->freeImage();
    }

    private function freeImage()
    {
        if (is_resource($this->image)) {
            imagedestroy($this->image);
        }

        if ($this->originalImage !== null) {
            if (is_resource($this->originalImage['image'])) {
                imagedestroy($this->originalImage['image']);
            }
            $this->originalImage = null;
        }
    }

    private function checkLoaded()
    {
        if (!is_resource($this->image)) {
            throw new Exception('Load image first');
        }
    }

    private function loadImage($file)
    {
        $result = [];


        if ($imageInfo = @getimagesize($file)) {
            $result['width'] = $imageInfo[0];
            $result['height'] = $imageInfo[1];

            $result['mimeType'] = $imageInfo['mime'];

            switch ($result['format'] = $imageInfo[2]) {
                case self::IMG_GIF:
                    if ($result['image'] = imagecreatefromgif($file)) {
                        return $result;
                    } else {
                        throw new Exception('Invalid image gif format');
                    }
                    break;
                case self::IMG_JPEG:
                    if ($result['image'] = imagecreatefromjpeg($file)) {
                        return $result;
                    } else {
                        throw new Exception('Invalid image jpeg format');
                    }
                    break;
                case self::IMG_PNG:
                    if ($result['image'] = imagecreatefrompng($file)) {
                        return $result;
                    } else {
                        throw new Exception('Invalid image png format');
                    }
                    break;
                default:
                    throw new Exception('Not supported image format');
            }
        } else {
            throw new Exception('Invalid image file');
        }


    }

    protected function initImage($image = false)
    {
        if ($image === false) {
            $image = $this->originalImage;
        }

        $this->width = $image['width'];
        $this->height = $image['height'];
        $this->mimeType = $image['mimeType'];
        $this->format = $image['format'];

        //Image
        if (is_resource($this->image))
            imagedestroy($this->image);

        $this->image = imagecreatetruecolor($this->width, $this->height);
        $this->preserveTransparency($this->image);
        imagecopy($this->image, $image['image'], 0, 0, 0, 0, $this->width, $this->height);
    }

    /**
     * @param $file
     *
     * @return CImageHandler
     * @throws Exception
     */
    public function load($file)
    {
        $this->freeImage();

        if (($this->originalImage = $this->loadImage($file))) {
            $this->initImage();
            $this->fileName = $file;


            return $this;
        } else {
            return false;
        }
    }

    public function reload()
    {
        $this->checkLoaded();
        $this->initImage();

        return $this;
    }

    private function preserveTransparency($newImage)
    {
        switch ($this->format) {
            case self::IMG_GIF:
                $color = imagecolorallocate(
                    $newImage,
                    $this->transparencyColor[0],
                    $this->transparencyColor[1],
                    $this->transparencyColor[2]
                );

                imagecolortransparent($newImage, $color);
                imagetruecolortopalette($newImage, false, 256);
                break;
            case self::IMG_PNG:
                imagealphablending($newImage, false);

                $color = imagecolorallocatealpha(
                    $newImage,
                    $this->transparencyColor[0],
                    $this->transparencyColor[1],
                    $this->transparencyColor[2],
                    0
                );

                imagefill($newImage, 0, 0, $color);
                imagesavealpha($newImage, true);
                break;
        }
    }

    public function resize($toWidth, $toHeight, $proportional = true)
    {
        $this->checkLoaded();

        $toWidth = $toWidth !== false ? $toWidth : $this->width;
        $toHeight = $toHeight !== false ? $toHeight : $this->height;

        if ($proportional) {
            $newHeight = $toHeight;
            $newWidth = round($newHeight / $this->height * $this->width);


            if ($newWidth > $toWidth) {
                $newWidth = $toWidth;
                $newHeight = round($newWidth / $this->width * $this->height);
            }
        } else {
            $newWidth = $toWidth;
            $newHeight = $toHeight;
        }

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        $this->preserveTransparency($newImage);

        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);


        imagedestroy($this->image);

        $this->image = $newImage;
        $this->width = $newWidth;
        $this->height = $newHeight;

        return $this;

    }

    public function thumb($toWidth, $toHeight, $proportional = true)
    {
        $this->checkLoaded();

        if ($toWidth !== false)
            $toWidth = min($toWidth, $this->width);

        if ($toHeight !== false)
            $toHeight = min($toHeight, $this->height);


        $this->resize($toWidth, $toHeight, $proportional);

        return $this;

    }

    public function watermark($watermarkFile, $offsetX, $offsetY, $corner = self::CORNER_RIGHT_BOTTOM, $zoom = false)
    {

        $this->checkLoaded();

        if ($wImg = $this->loadImage($watermarkFile)) {

            $posX = 0;
            $posY = 0;

            $watermarkWidth = $wImg['width'];
            $watermarkHeight = $wImg['height'];

            if ($zoom !== false) {
                $dimension = round(max($this->width, $this->height) * $zoom);

                $watermarkHeight = $dimension;
                $watermarkWidth = round($watermarkHeight / $wImg['height'] * $wImg['width']);

                if ($watermarkWidth > $dimension) {
                    $watermarkWidth = $dimension;
                    $watermarkHeight = round($watermarkWidth / $wImg['width'] * $wImg['height']);
                }
            }

            switch ($corner) {
                case self::CORNER_LEFT_TOP:
                    $posX = $offsetX;
                    $posY = $offsetY;
                    break;
                case self::CORNER_RIGHT_TOP:
                    $posX = $this->width - $watermarkWidth - $offsetX;
                    $posY = $offsetY;
                    break;
                case self::CORNER_LEFT_BOTTOM:
                    $posX = $offsetX;
                    $posY = $this->height - $watermarkHeight - $offsetY;
                    break;
                case self::CORNER_RIGHT_BOTTOM:
                    $posX = $this->width - $watermarkWidth - $offsetX;
                    $posY = $this->height - $watermarkHeight - $offsetY;
                    break;
                case self::CORNER_CENTER:
                    $posX = floor(($this->width - $watermarkWidth) / 2);
                    $posY = floor(($this->height - $watermarkHeight) / 2);
                    break;
                case self::CORNER_CENTER_TOP:
                    $posX = floor(($this->width - $watermarkWidth) / 2);
                    $posY = $offsetY;
                    break;
                case self::CORNER_CENTER_BOTTOM:
                    $posX = floor(($this->width - $watermarkWidth) / 2);
                    $posY = $this->height - $watermarkHeight - $offsetY;
                    break;
                case self::CORNER_LEFT_CENTER:
                    $posX = $offsetX;
                    $posY = floor(($this->height - $watermarkHeight) / 2);
                    break;
                case self::CORNER_RIGHT_CENTER:
                    $posX = $this->width - $watermarkWidth - $offsetX;
                    $posY = floor(($this->height - $watermarkHeight) / 2);
                    break;
                default:
                    throw new Exception('Invalid $corner value');
            }

            imagecopyresampled(
                $this->image,
                $wImg['image'],
                $posX,
                $posY,
                0,
                0,
                $watermarkWidth,
                $watermarkHeight,
                $wImg['width'],
                $wImg['height']
            );


            imagedestroy($wImg['image']);

            return $this;
        } else {
            return false;
        }
    }


    public function flip($mode)
    {
        $this->checkLoaded();

        $srcX = 0;
        $srcY = 0;
        $srcWidth = $this->width;
        $srcHeight = $this->height;

        switch ($mode) {
            case self::FLIP_HORIZONTAL:
                $srcX = $this->width - 1;
                $srcWidth = -$this->width;
                break;
            case self::FLIP_VERTICAL:
                $srcY = $this->height - 1;
                $srcHeight = -$this->height;
                break;
            case self::FLIP_BOTH:
                $srcX = $this->width - 1;
                $srcY = $this->height - 1;
                $srcWidth = -$this->width;
                $srcHeight = -$this->height;
                break;
            default:
                throw new Exception('Invalid $mode value');
        }

        $newImage = imagecreatetruecolor($this->width, $this->height);
        $this->preserveTransparency($newImage);

        imagecopyresampled($newImage, $this->image, 0, 0, $srcX, $srcY, $this->width, $this->height, $srcWidth, $srcHeight);

        imagedestroy($this->image);

        $this->image = $newImage;
        //dimensions not changed

        return $this;
    }

    public function rotate($degrees)
    {
        $this->checkLoaded();

        $degrees = (int)$degrees;
        $this->image = imagerotate($this->image, $degrees, 0);

        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);

        return $this;
    }

    public function crop($width, $height, $startX = false, $startY = false)
    {
        $this->checkLoaded();

        $width = (int)$width;
        $height = (int)$height;

        //Centered crop
        $startX = $startX === false ? floor(($this->width - $width) / 2) : intval($startX);
        $startY = $startY === false ? floor(($this->height - $height) / 2) : intval($startY);

        //Check dimensions
        $startX = max(0, min($this->width, $startX));
        $startY = max(0, min($this->height, $startY));
        $width = min($width, $this->width - $startX);
        $height = min($height, $this->height - $startY);


        $newImage = imagecreatetruecolor($width, $height);

        $this->preserveTransparency($newImage);

        imagecopyresampled($newImage, $this->image, 0, 0, $startX, $startY, $width, $height, $width, $height);

        imagedestroy($this->image);

        $this->image = $newImage;
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function text($text, $fontFile, $size = 12, $color = [0, 0, 0],
                         $corner = self::CORNER_LEFT_TOP, $offsetX = 0, $offsetY = 0, $angle = 0, $alpha = 0)
    {
        $this->checkLoaded();

        $bBox = imagettfbbox($size, $angle, $fontFile, $text);
        $textHeight = $bBox[1] - $bBox[7];
        $textWidth = $bBox[2] - $bBox[0];


        switch ($corner) {
            case self::CORNER_LEFT_TOP:
                $posX = $offsetX;
                $posY = $offsetY;
                break;
            case self::CORNER_RIGHT_TOP:
                $posX = $this->width - $textWidth - $offsetX;
                $posY = $offsetY;
                break;
            case self::CORNER_LEFT_BOTTOM:
                $posX = $offsetX;
                $posY = $this->height - $textHeight - $offsetY;
                break;
            case self::CORNER_RIGHT_BOTTOM:
                $posX = $this->width - $textWidth - $offsetX;
                $posY = $this->height - $textHeight - $offsetY;
                break;
            case self::CORNER_CENTER:
                $posX = floor(($this->width - $textWidth) / 2);
                $posY = floor(($this->height - $textHeight) / 2);
                break;
            case self::CORNER_CENTER_TOP:
                $posX = floor(($this->width - $textWidth) / 2);
                $posY = $offsetY;
                break;
            case self::CORNER_CENTER_BOTTOM:
                $posX = floor(($this->width - $textWidth) / 2);
                $posY = $this->height - $textHeight - $offsetY;
                break;
            case self::CORNER_LEFT_CENTER:
                $posX = $offsetX;
                $posY = floor(($this->height - $textHeight) / 2);
                break;
            case self::CORNER_RIGHT_CENTER:
                $posX = $this->width - $textWidth - $offsetX;
                $posY = floor(($this->height - $textHeight) / 2);
                break;
            default:
                throw new Exception('Invalid $corner value');
        }

        if ($alpha > 0) {
            $color = imagecolorallocatealpha($this->image, $color[0], $color[1], $color[2], $alpha);
        } else {
            $color = imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
        }

        imagettftext($this->image, $size, $angle, $posX, $posY + $textHeight, $color, $fontFile, $text);

        return $this;
    }

    public function adaptiveThumb($width, $height)
    {
        $this->checkLoaded();

        $width = intval($width);
        $height = intval($height);

        $widthProportion = $width / $this->width;
        $heightProportion = $height / $this->height;

        if ($widthProportion > $heightProportion) {
            $newWidth = $width;
            $newHeight = round($newWidth / $this->width * $this->height);
        } else {
            $newHeight = $height;
            $newWidth = round($newHeight / $this->height * $this->width);
        }

        $this->resize($newWidth, $newHeight);

        $this->crop($width, $height);

        return $this;
    }

    public function resizeCanvas($toWidth, $toHeight, $backgroundColor = [255, 255, 255])
    {
        $this->checkLoaded();

        $newWidth = min($toWidth, $this->width);
        $newHeight = min($toHeight, $this->height);

        $widthProportion = $newWidth / $this->width;
        $heightProportion = $newHeight / $this->height;

        if ($widthProportion < $heightProportion) {
            $newHeight = round($widthProportion * $this->height);
        } else {
            $newWidth = round($heightProportion * $this->width);
        }

        $posX = floor(($toWidth - $newWidth) / 2);
        $posY = floor(($toHeight - $newHeight) / 2);


        $newImage = imagecreatetruecolor($toWidth, $toHeight);

        $backgroundColor = imagecolorallocate($newImage, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
        imagefill($newImage, 0, 0, $backgroundColor);

        imagecopyresampled($newImage, $this->image, $posX, $posY, 0, 0, $newWidth, $newHeight, $this->width, $this->height);

        imagedestroy($this->image);

        $this->image = $newImage;
        $this->width = $toWidth;
        $this->height = $toHeight;

        return $this;
    }

    public function grayscale()
    {
        $newImage = imagecreatetruecolor($this->width, $this->height);

        imagecopy($newImage, $this->image, 0, 0, 0, 0, $this->width, $this->height);
        imagecopymergegray($newImage, $newImage, 0, 0, 0, 0, $this->width, $this->height, 0);

        imagedestroy($this->image);

        $this->image = $newImage;

        return $this;
    }

    public function show($inFormat = false, $jpegQuality = 75)
    {
        $this->checkLoaded();

        if (!$inFormat) {
            $inFormat = $this->format;
        }

        switch ($inFormat) {
            case self::IMG_GIF:
                header('Content-type: image/gif');
                imagegif($this->image);
                break;
            case self::IMG_JPEG:
                header('Content-type: image/jpeg');
                imagejpeg($this->image, null, $jpegQuality);
                break;
            case self::IMG_PNG:
                header('Content-type: image/png');
                imagepng($this->image);
                break;
            default:
                throw new Exception('Invalid image format for putput');
        }

        return $this;
    }

    public function save($file = false, $toFormat = false, $jpegQuality = 75, $touch = false)
    {
        if (empty($file)) {
            $file = $this->fileName;
        }

        $this->checkLoaded();

        if (!$toFormat) {
            $toFormat = $this->format;
        }

        switch ($toFormat) {
            case self::IMG_GIF:
                if (!imagegif($this->image, $file)) {
                    throw new Exception('Can\'t save gif file');
                }
                break;
            case self::IMG_JPEG:
                if (!imagejpeg($this->image, $file, $jpegQuality)) {
                    throw new Exception('Can\'t save jpeg file');
                }
                break;
            case self::IMG_PNG:
                if (!imagepng($this->image, $file)) {
                    throw new Exception('Can\'t save png file');
                }
                break;
            default:
                throw new Exception('Invalid image format for save');
        }

        if ($touch && $file != $this->fileName) {
            touch($file, filemtime($this->fileName));
        }

        return $this;
    }

}