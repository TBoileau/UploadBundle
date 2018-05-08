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


        $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $file = new UploadedFile($this->copy(), 'file.png', 'image/png', 2051);
        $client->request('POST', '/t_boileau_upload/upload', ["attr" => json_encode(["mimeTypes" => ["image/png"], "maxSize" => "1M", "image" => []])], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('POST', '/t_boileau_upload/upload', [], []);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $file = new UploadedFile($this->copy(), 'fail', '', 1);
        $client->request('POST', '/t_boileau_upload/upload', ["attr" => json_encode(["mimeTypes" => ["image/png"], "maxSize" => "1M", "image" => []])], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $file = new UploadedFile($this->copy(), 'file.png', 'image/png', 2051);
        $client->request('POST', '/t_boileau_upload/upload', ["attr" => json_encode(["mimeTypes" => ["video/x-msvideo"], "maxSize" => "1M", "image" => []])], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $file = new UploadedFile($this->copy(), 'file.png', 'image/png', 2051);
        $client->request('POST', '/t_boileau_upload/upload', ["attr" => json_encode(["mimeTypes" => ["image/png"], "maxSize" => "1K", "image" => []])], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $file = new UploadedFile($this->copy(), 'file.png', 'image/png', 2051);
        $client->request('POST', '/t_boileau_upload/upload', ["attr" => json_encode(["mimeTypes" => ["image/png"], "maxSize" => "1M", "image" => ["min_ratio" => 3]])], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $file = new UploadedFile($this->copy(), 'file.png', 'image/png', 2051);
        $client->request('POST', '/t_boileau_upload/upload', ["attr" => json_encode(["mimeTypes" => ["image/png"], "maxSize" => "1M", "image" => ["max_ratio" => 2]])], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $file = new UploadedFile($this->copy(), 'file.png', 'image/png', 2051);
        $client->request('POST', '/t_boileau_upload/upload', ["attr" => json_encode(["mimeTypes" => ["image/png"], "maxSize" => "1M", "image" => ["min_width" => 800]])], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $file = new UploadedFile($this->copy(), 'file.png', 'image/png', 2051);
        $client->request('POST', '/t_boileau_upload/upload', ["attr" => json_encode(["mimeTypes" => ["image/png"], "maxSize" => "1M", "image" => ["max_width" => 500]])], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $file = new UploadedFile($this->copy(), 'file.png', 'image/png', 2051);
        $client->request('POST', '/t_boileau_upload/upload', ["attr" => json_encode(["mimeTypes" => ["image/png"], "maxSize" => "1M", "image" => ["min_height" => 400]])], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $file = new UploadedFile($this->copy(), 'file.png', 'image/png', 2051);
        $client->request('POST', '/t_boileau_upload/upload', ["attr" => json_encode(["mimeTypes" => ["image/png"], "maxSize" => "1M", "image" => ["max_height" => 200]])], ["file" => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @return string
     */
    private function copy()
    {
        $filename = __DIR__.'/../Resources/img/file.png';
        copy(__DIR__.'/../Resources/img/750x300.png', $filename);
        return $filename;
    }
}