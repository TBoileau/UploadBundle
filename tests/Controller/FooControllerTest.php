<?php
/**
 * Created by PhpStorm.
 * User: tboileau-desktop
 * Date: 08/05/18
 * Time: 02:41
 */

namespace TBoileau\FormHandlerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FooControllerTest extends WebTestCase
{
    public function testFoo()
    {
        $client = static::createClient();

        $file = new UploadedFile(__DIR__.'/../Resources/img/750x300.png', '750x300.jpg', 'image/jpeg', 2051);

        $client->request('POST', '/t_boileau_upload/upload', [], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}