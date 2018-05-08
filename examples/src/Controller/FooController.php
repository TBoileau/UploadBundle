<?php
/**
 * Created by PhpStorm.
 * User: tboileau-desktop
 * Date: 08/05/18
 * Time: 02:38
 */

namespace App\Controller;

use App\Form\FooType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FooController extends Controller
{
    /**
     * @return Response
     * @Route("/", name="index")
     */
    public function foo()
    {
        return $this->render('default/index.html.twig', [
            'form' => $this->createForm(FooType::class)->createView(),
        ]);
    }
}