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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TBoileau\UploadBundle\Model\File;

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
     * @var ValidatorInterface
     */
    private $validator;

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
     * @param ValidatorInterface $validator
     * @param string $uploadDir
     * @param string $webPath
     */
    public function __construct(RequestStack $requestStack, ValidatorInterface $validator, string $uploadDir, string $webPath)
    {
        $this->requestStack = $requestStack;
        $this->validator = $validator;
        $this->uploadDir = $uploadDir;
        $this->webPath = $webPath;
    }

    /**
     * @return JsonResponse
     */
    public function __invoke()
    {
        try {
            $file = new File();
            $file->setUploadDir($this->uploadDir);
            $file->setFile($this->requestStack->getCurrentRequest()->files->get("file"));
            $file->setAttributes(json_decode($this->requestStack->getCurrentRequest()->request->get("attr"), true));

            if(count($violationsList = $this->validator->validate($file)) == 0) {
                return new JsonResponse(["error" => false, "file" => $this->webPath."/".$file->upload()]);
            }else{
                return new JsonResponse(["error" => true, "message" => $violationsList->get(0)->getMessage()]);
            }
        }catch(\Exception $e) {
            return new JsonResponse(["error" => true, "message" => "error"]);
        }
    }
}