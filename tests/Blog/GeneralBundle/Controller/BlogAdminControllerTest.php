<?php

namespace Tests\BlogGeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogAdminControllerTest extends WebTestCase
{

    public function testAdmin() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('_submit')->form(array(
            '_username'  => 'admin',
            '_password'  => 'admin',
        ));
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $urlsAdmin = array(
            ('/admin'),
            ('/admin/viewlistallcomment/1'),
            ('/admin/viewlistallarticle/1'),
            ('/admin/viewlistallreport/1'),
            ('/admin/configuration'),
        );

        foreach ($urlsAdmin as $url)
        {
            $client->request('GET', $url);
            $this->assertTrue($client->getResponse()->isSuccessful());
        }

    }


}
