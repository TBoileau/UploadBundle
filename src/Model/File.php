<?php

namespace TBoileau\UploadBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class File
 * @package TBoileau\UploadBundle\Model
 * @author Thomas Boileau <t-boileau@email.com>
 */
class File
{

    /**
     * @var UploadedFile
     * @Assert\NotNull(message="noFile")
     * @Assert\Expression(
     *     "this.getFile() != null and this.getFile().getMimeType() in this.getMimeTypes()",
     *     message="mimeTypeError"
     * )
     * @Assert\Expression(
     *     "this.getFile() != null and this.getFile().getClientSize() < this.getMaxSize()",
     *     message="sizeTooBig"
     * )
     * @Assert\Expression(
     *     "this.getFile() != null and (!this.isImage() or (this.isImage() and (this.getImage('min_ratio') == -1 or this.getRatio() >= this.getImage('min_ratio'))))",
     *     message="imgRatioTooSmall"
     * )
     * @Assert\Expression(
     *     "this.getFile() != null and (!this.isImage() or (this.isImage() and (this.getImage('min_height') == -1 or this.getHeight() >= this.getImage('min_height'))))",
     *     message="imgTooSmall"
     * )
     * @Assert\Expression(
     *     "this.getFile() != null and (!this.isImage() or (this.isImage() and (this.getImage('min_width') == -1 or this.getWidth() >= this.getImage('min_width'))))",
     *     message="imgTooSmall"
     * )
     * @Assert\Expression(
     *     "this.getFile() != null and (!this.isImage() or (this.isImage() and (this.getImage('max_ratio') == -1 or this.getRatio() <= this.getImage('max_ratio'))))",
     *     message="imgRatioTooBig"
     * )
     * @Assert\Expression(
     *     "this.getFile() != null and (!this.isImage() or (this.isImage() and (this.getImage('max_height') == -1 or this.getHeight() <= this.getImage('max_height'))))",
     *     message="imgTooBig"
     * )
     * @Assert\Expression(
     *     "this.getFile() != null and (!this.isImage() or (this.isImage() and (this.getImage('max_width') == -1 or this.getWidth() <= this.getImage('max_width'))))",
     *     message="imgTooBig"
     * )
     */
    private $file;

    /**
     * @var array
     * @Assert\Count(min=0)
     */
    private $mimeTypes = [];

    /**
     * @var string
     * @Assert\GreaterThanOrEqual(value=0)
     * @Assert\Regex(pattern="/^\d+[O|K|M]$/", message="maxSizeRegex")
     */
    private $maxSize;

    /**
     * @var array
     * @Assert\Count(min=0)
     * @Assert\Collection(
     *      fields = {
     *          "min_ratio" = @Assert\Optional(@Assert\GreaterThanOrEqual(value=0)),
     *          "max_ratio" = @Assert\Optional(@Assert\GreaterThanOrEqual(value=0)),
     *          "min_width" = @Assert\Optional(@Assert\GreaterThanOrEqual(value=0)),
     *          "max_width" = @Assert\Optional(@Assert\GreaterThanOrEqual(value=0)),
     *          "min_height" = @Assert\Optional(@Assert\GreaterThanOrEqual(value=0)),
     *          "max_height" = @Assert\Optional(@Assert\GreaterThanOrEqual(value=0)),
     *      },
     *      allowMissingFields = true,
     *      allowExtraFields = false,
     *      extraFieldsMessage = "tooManyAttributes",
     * )
     */
    private $image;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * @var bool
     */
    private $isImage;

    /**
     * @var float|null
     */
    private $ratio;

    /**
     * @var float|null
     */
    private $width;

    /**
     * @var float|null
     */
    private $height;

    /**
     * @return string
     */
    public function upload(): string
    {
        $fileName = md5(uniqid()).'.'.$this->file->getClientOriginalExtension();
        $this->file->move($this->uploadDir, $fileName);
        return $fileName;
    }

    /**
     * @return UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(?UploadedFile $file)
    {
        $this->file = $file;
        if($this->file and $this->isImage = in_array($this->file->getMimeType(), ["image/jpg", "image/jpeg", "image/png", "image/gif"]) === true) {
            list($this->width, $this->height) = getimagesize($this->file);
            $this->ratio = $this->width/$this->height;
        }
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(?array $attributes = [])
    {
        [
            "mimeTypes" => $this->mimeTypes,
            "image" => $this->image,
            "maxSize" => $this->maxSize
        ] = $attributes;
    }

    /**
     * @param string $uploadDir
     */
    public function setUploadDir(string $uploadDir): void
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * @return float|int
     */
    public function getMaxSize()
    {
        preg_match("/^(\d+)([O|K|M]{1})$/", $this->maxSize, $matches);
        return $matches[1] * ($matches[2] == "O" ? 1 : ($matches[2] == "K" ? 1024 : 1024*1024));
    }

    /**
     * @return array
     */
    public function getMimeTypes(): array
    {
        return $this->mimeTypes;
    }

    /**
     * @param string $attr
     * @return int
     */
    public function getImage(string $attr): int
    {
        return $this->image[$attr] ?? -1;
    }

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        return $this->isImage;
    }

    /**
     * @return float|null
     */
    public function getRatio(): ?float
    {
        return $this->ratio;
    }

    /**
     * @return float|null
     */
    public function getWidth(): ?float
    {
        return $this->width;
    }

    /**
     * @return float|null
     */
    public function getHeight(): ?float
    {
        return $this->height;
    }
}