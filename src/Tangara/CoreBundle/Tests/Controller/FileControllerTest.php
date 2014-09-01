<?php

namespace Tangara\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCase;
use org\bovigo\vfs\vfsStream;

class FileControllerTest extends WebTestCase {

    protected function setUp() {
        vfsStream::setup();
    }

    public function testIndex() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue(true);
    }

    /**
     * @param string $className
     * @dataProvider provider
     */
    public function testGeneratesTestCodeCorrectly($className) {
        $generatedFile = vfsStream::url('root') . '/' . $className . 'Test.php';
        $generator = new TestGenerator(
                $className, __DIR__ . '/_fixture/_input/' . $className . '.php', $className . 'Test', $generatedFile
        );
        $generator->write();
        $this->assertStringMatchesFormatFile(
                __DIR__ . '/_fixture/_expected/' . $className . 'Test.php', file_get_contents($generatedFile)
        );
    }

    public function provider() {
        return array(
            array('Calculator'),
            array('Calculator2')
        );
    }

}
