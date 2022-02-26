<?php

namespace Test\EndToEnd;

class ApiTest extends EndToEndTestCase
{
    public function teste_should_get_404_where_not_found_route()
    {
        // When
        $httpResponse = $this->httpGet("this-route-no-exists");
        $jsonResponse = $httpResponse->getBody()->getContents();
        $arrayResponse = json_decode($jsonResponse, true);

        // Then
        $this->assertEquals(404, $httpResponse->getStatusCode());
        $this->assertIsArray($arrayResponse);
        $this->assertArrayHasKey('error', $arrayResponse);
        $this->assertEquals('Not found', $arrayResponse['error']);
    }
}