<?php

namespace Fsb\CalendarBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        
        $this->assertEquals(302, $client->getResponse()->getStatusCode(),
        		'redirect to the calendar_month view for today (status 302)'
        );
    }
}
