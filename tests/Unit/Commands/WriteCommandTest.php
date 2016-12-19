<?php

namespace VaultPhp\Test\Unit;

use GuzzleHttp\Psr7\Request;
use VaultPhp\Client;
use VaultPhp\Commands\WriteCommand;
use VaultPhp\Responses\WriteResponse;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

class WriteCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testRunSendsCorrectRequestAndReturnsCorrectResponse()
    {
        $expectedApiVersion = 0;
        $expectedEndpoint   = 'https://localhost';
        $expectedToken      = 'asds89j1239e';
        $expectedUriPath    = 'secret/test';
        $expectedPayload    = ['cert' => '234r43fgrsgdfg'];
        $expectedMethod     = 'POST';
        $expectedParams     = ['json' => $expectedPayload];

        $expectedGuzzleRequest = new Request(
            $expectedMethod,
            $expectedEndpoint . '/v' . $expectedApiVersion . '/' . ltrim($expectedUriPath, '/'),
            ['X-Vault-Token' => $expectedToken]
        );
        $expectedGuzzleResponse = new GuzzleResponse();

        $guzzleClient = $this->getMock(GuzzleClient::class, ['send']);
        $guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($expectedGuzzleRequest, $expectedParams)
            ->will($this->returnValue($expectedGuzzleResponse));

        $client = new Client($guzzleClient, [
            'version'  => $expectedApiVersion,
            'endpoint' => $expectedEndpoint,
            'token'    => $expectedToken,
        ]);

        $writeCommand     = new WriteCommand($client, [$expectedUriPath, $expectedPayload]);
        $expectedResponse = new WriteResponse($writeCommand, $expectedGuzzleResponse);

        $this->assertEquals($expectedResponse, $writeCommand->run());
    }
}
