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
     * The image object or path.
     *
     * @var \Intervention\Image\Image|string
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
        $width = 100,
        $height = 100,
        $type = 'jpg',
        $fit = self::FIT_STRETCH,
        $background = '#fff'
    ) {
        $this->imageManager = new ImageManager();

        $this->image = $image;
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
        $imagePath = (is_object($this->image) && get_class($this->image) == UploadedFile::class)
            ? $this->image->getRealPath() : $this->image;

        switch ($this->fit) {
            case self::FIT_CENTER:
                $image = $this->imageManager->make($imagePath)->resize($this->width, $this->height, function ($c) {
                    $c->aspectRatio();
                });

                $backgroundImage = $this->imageManager->canvas($this->width, $this->height, $this->background);
                $backgroundImage->insert($image, 'center');

                $this->encodedImage = (string) $backgroundImage->encode($type ?: $this->type, $quality);

                break;

            case self::FIT_CROP:
                $this->encodedImage = (string) $this->imageManager->make($imagePath)
                    ->fit($this->width, $this->height, function ($c) {
                        $c->upsize();
                    })->resize($this->width, $this->height, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    })->encode($type ?: $this->type, $quality);

                break;

            default:
                $this->encodedImage = (string) $this->imageManager->make($imagePath)
                    ->resize($this->width, $this->height)
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
}
