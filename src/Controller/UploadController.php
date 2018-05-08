<?php

namespace TBoileau\UploadBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TBoileau\UploadBundle\Handler\UploadHandler;

/**
 * Class UploadController
 * @package TBoileau\UploadBundle\Controller
 * @author Thomas Boileau <t-boileau@email.com>
 */
class UploadController extends Controller
{
    /**
     * @Route("/upload")
     * @return JsonResponse
     */
    public function upload()
    {
        return $this->get("t_boileau_upload.handler.upload")();
    }
}