<?php

namespace Tangara\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase {

    public function testIndex() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        return $this->assertTrue(true);
        
//        $this->markTestSkipped(
//                'The MySQLi extension is not available.'
//        );
    }

}
