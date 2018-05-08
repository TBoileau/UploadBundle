<?php
/**
 * Created by PhpStorm.
 * User: tboileau-desktop
 * Date: 08/05/18
 * Time: 10:58
 */

namespace TBoileau\UploadBundle\Handler;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class UploadHandler
 * @package TBoileau\UploadBundle\Handler
 * @author Thomas Boileau <t-boileau@email.com>
 */
class UploadHandler
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * @var string
     */
    private $webPath;

    /**
     * UploadHandler constructor.
     * @param RequestStack $requestStack
     * @param string $uploadDir
     * @param string $webPath
     */
    public function __construct(RequestStack $requestStack, string $uploadDir, string $webPath)
    {
        $this->requestStack = $requestStack;
        $this->uploadDir = $uploadDir;
        $this->webPath = $webPath;
    }

    /**
     * @return JsonResponse
     */
    public function __invoke()
    {
        try {
            $file = $this->requestStack->getCurrentRequest()->files->get("file");
            if($file === null) {
                throw new BadRequestHttpException('No file upload.');
            }

            $attributes = json_decode($this->requestStack->getCurrentRequest()->request->get("attr"), true);

            if($attributes["mimeTypes"] && !in_array($file->getMimeType() ,$attributes["mimeTypes"])) {
                return new JsonResponse(["error" => true, "message" => "Your file is not valid."]);
            }

            if(preg_match("/^(\d+)([O|K|M]{1})$/", $attributes["maxSize"], $matches)) {
                $maxSize = $matches[1] * ($matches[2] == "O" ? 1 : ($matches[2] == "K" ? 1024 : 1024*1024));
                if($maxSize < $file->getClientSize()) {
                    return new JsonResponse(["error" => true, "message" => "Your file is too big."]);
                }
            }

            if(in_array($file->getMimeType(), ["image/jpg", "image/jpeg", "image/png", "image/gif"])) {
                list($width, $height) = getimagesize($file);
                if(isset($attributes["image"]["min_ratio"]) && $attributes["image"]["min_ratio"] > $width/$height) {
                    return new JsonResponse(["error" => true, "message" => "The ratio of your image is too small."]);
                }
                if(isset($attributes["image"]["max_ratio"]) && $attributes["image"]["max_ratio"] < $width/$height) {
                    return new JsonResponse(["error" => true, "message" => "The ratio of your image is too big."]);
                }
                if(isset($attributes["image"]["min_width"]) && $attributes["image"]["min_width"] > $width/$height) {
                    return new JsonResponse(["error" => true, "message" => "Your image is too small."]);
                }
                if(isset($attributes["image"]["max_width"]) && $attributes["image"]["max_width"] < $width) {
                    return new JsonResponse(["error" => true, "message" => "Your image is too big."]);
                }
                if(isset($attributes["image"]["min_height"]) && $attributes["image"]["min_height"] > $height) {
                    return new JsonResponse(["error" => true, "message" => "Your image is too small."]);
                }
                if(isset($attributes["image"]["max_height"]) && $attributes["image"]["max_height"] < $height) {
                    return new JsonResponse(["error" => true, "message" => "Your image is too big."]);
                }
            }

            $fileName = md5(uniqid()).'.'.$file->getClientOriginalExtension();
            $file->move($this->uploadDir, $fileName);

            return new JsonResponse(["error" => false, "file" => $this->webPath."/".$fileName]);
        }catch(\Exception $e) {
            return new JsonResponse(["error" => true, "message" => $e->getMessage()]);
        }
    }
}