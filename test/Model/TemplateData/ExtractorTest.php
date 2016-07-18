<?php

namespace Droid\Test\Model\TemplateData;

use Droid\Model\Inventory\Host;
use Droid\Model\TemplateData\Extractor;

class ExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->extractor = new Extractor;
    }

    public function testExtractDataFromHost()
    {
        $expectedData = array(
            'name' => 'my-host',
            'address' => 'host.example.com',
            'public_ip' => '192.0.2.1',
            'private_ip' => '203.0.113.1',
            'variables' => array(
                'role' => 'master',
                'more' => array('more' => 'more')
            ),
        );
        $host = new Host($expectedData['name']);
        $host
            ->setAddress($expectedData['address'])
            ->setPublicIp($expectedData['public_ip'])
            ->setPrivateIp($expectedData['private_ip'])
        ;
        foreach ($expectedData['variables'] as $k => $v) {
            $host->setVariable($k, $v);
        }

        $this->assertEquals(
            $expectedData,
            $this->extractor->extract($host, array('TemplateData'))
        );
    }
}
