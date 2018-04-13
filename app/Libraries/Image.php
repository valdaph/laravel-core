<?php

namespace Valda\Libraries;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class Image
{
    /**
     * Constant for stretch fit.
     */
    const FIT_STRETCH = 1;

    /**
     * Constant for center fit.
     */
    const FIT_CENTER = 2;

    /**
     * Constant for crop fit.
     */
    const FIT_CROP = 3;

    /**
     * Constant for widen.
     */
    const FIT_WIDEN = 4;

    /**
     * Constant for heighten.
     */
    const FIT_HEIGHTEN = 5;

    /**
     * The image instance.
     *
     * @var mixed
     */
    private $image;

    /**
     * The encoded image.
     *
     * @var string
     */
    private $encodedImage;

    /**
     * The image manager object.
     *
     * @var \Intervention\Image\ImageManager
     */
    private $imageManager;

    /**
     * The image's width.
     *
     * @var integer
     */
    private $width;

    /**
     * The image's height.
     *
     * @var integer
     */
    private $height;

    /**
     * The image's type.
     *
     * @var string
     */
    private $type;

    /**
     * The type of how the image will be fitted.
     *
     * @var integer
     */
    private $fit;

    /**
     * The background color.
     *
     * @var string
     */
    private $background;

    /**
     * Creates a new Image instance
     *
     * @param  \Illuminate\Http\UploadedFile|string  $image
     * @param  integer  $width
     * @param  integer  $height
     * @param  string  $type
     * @param  integer  $type
     * @param  string  $background
     */
    public function __construct(
        $image,
        $width = null,
        $height = null,
        $type = 'jpg',
        $fit = null,
        $background = '#fff'
    ) {
        $this->imageManager = new ImageManager();

        $this->image = $this->getImage($image);
        $this->width = $width;
        $this->height = $height;
        $this->type = $type;
        $this->fit = $fit;
        $this->background = $background;
    }

    /**
     * Encode the image.
     *
     * @param  integer  $quality
     * @param  string|null  $type
     * @return string
     */
    public function encode($quality = 85, $type = null)
    {
        $width = $this->getWidth();
        $height = $this->getHeight();

        switch ($this->fit) {
            case self::FIT_STRETCH:
                $this->encodedImage = (string) $this->image
                    ->resize($width, $height)
                    ->encode($type ?: $this->type, $quality);

                break;

            case self::FIT_CENTER:
                $image = $this->image->resize($width, $height, function ($c) {
                    $c->aspectRatio();
                });

                $backgroundImage = $this->imageManager->canvas($width, $height, $this->background);
                $backgroundImage->insert($image, 'center');

                $this->encodedImage = (string) $backgroundImage->encode($type ?: $this->type, $quality);

                break;

            case self::FIT_CROP:
                $this->encodedImage = (string) $this->image
                    ->fit($width, $height, function ($c) {
                        $c->upsize();
                    })->resize($width, $height, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    })->encode($type ?: $this->type, $quality);

                break;
            
            case self::FIT_WIDEN:
                $this->encodedImage = (string) $this->image
                    ->widen($this->getWidth())
                    ->encode($type ?: $this->type, $quality);

                break;

            case self::FIT_HEIGHTEN:
                $this->encodedImage = (string) $this->image
                    ->heighten($this->getHeight())
                    ->encode($type ?: $this->type, $quality);

                break;

            default:
                $this->encodedImage = (string) $this->image
                    ->encode($type ?: $this->type, $quality);

                break;
        }

        return $this->encodedImage;
    }

    /**
     * Center fit the image.
     *
     * @param  string|null  $background
     * @return $this
     */
    public function fitCenter($background = null)
    {
        $this->fit = self::FIT_CENTER;
        $this->background = $background ?: $this->background;

        return $this;
    }

    /**
     * Crop fit the image.
     *
     * @return $this
     */
    public function fitCrop()
    {
        $this->fit = self::FIT_CROP;

        return $this;
    }

    /**
     * Stretch fit the image.
     *
     * @return $this
     */
    public function fitStretch()
    {
        $this->fit = self::FIT_STRETCH;

        return $this;
    }

    /**
     * Get the image's height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height ?: $this->image->getHeight();
    }

    /**
     * Get the image instance.
     *
     * @param  \Illuminate\Http\UploadedFile|string  $image
     * @return string
     */
    public function getImage($image)
    {
        $imagePath = is_object($image) && get_class($image) === UploadedFile::class
            ? $image->getRealPath()
            : $image;

        return $this->imageManager->make($imagePath);
    }

    /**
     * Get the image's width.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width ?: $this->image->getWidth();
    }

    /**
     * Heighten the image to a given height.
     *
     * @param  int  $height
     * @param  bool  $force
     * @return $this
     */
    public function heighten($height, $force = false)
    {
        if ($force) {
            $this->height = $height;
        } else {
            $this->height = $this->getHeight() >= $height
                ? $this->getHeight()
                : $height;
        }

        $this->fit = self::FIT_HEIGHTEN;

        return $this;
    }

    /**
     * Heighten the image to a given height if it exceeds it.
     *
     * @param  int  $height
     * @return $this
     */
    public function heightenMax($height)
    {
        $this->height = $this->getHeight() >= $height
            ? $height
            : $this->getHeight();

        $this->fit = self::FIT_HEIGHTEN;

        return $this;
    }

    /**
     * Store the image.
     *
     * @param  string  $path
     * @param  mixed  $options
     * @return bool
     */
    public function put($path, $options = [])
    {
        $encodedImage = $this->encodedImage ?: $this->encode();

        return Storage::put($path, $encodedImage, $options);
    }

    /**
     * Set the image's background color.
     *
     * @param  string  $background
     * @return $this
     */
    public function setBackground($background)
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Set the image's dimensions.
     *
     * @param  integer  $width
     * @param  integer  $height
     * @return $this
     */
    public function setDimensions($width, $height)
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    /**
     * Set the image's height.
     *
     * @param  integer  $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Set the image's type.
     *
     * @param  string  $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the image's width.
     *
     * @param  integer  $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Widen the image to a given width.
     *
     * @param  int  $width
     * @param  bool  $force
     * @return $this
     */
    public function widen($width, $force = false)
    {
        if ($force) {
            $this->width = $width;
        } else {
            $this->width = $this->getWidth() >= $width
                ? $this->getWidth()
                : $width;
        }

        $this->fit = self::FIT_WIDEN;

        return $this;
    }

    /**
     * Widen the image to a given width if it exceeds it.
     *
     * @param  int  $width
     * @return $this
     */
    public function widenMax($width)
    {
        $this->width = $this->getWidth() >= $width
            ? $width
            : $this->getWidth();

        $this->fit = self::FIT_WIDEN;

        return $this;
    }
}
