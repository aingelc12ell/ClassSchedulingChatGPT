<?php

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Environment;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;
use Slim\Psr7\Stream;

class ApiIntegrationTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = AppFactory::create();

        // Include routes and dependencies
        require __DIR__ . '/../src/routes.php';
        require __DIR__ . '/../src/dependencies.php';
    }

    private function createRequest(string $method, string $path, array $headers = [], array $payload = []): Request
    {
        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = new Stream($handle);

        if (!empty($payload)) {
            $stream->write(json_encode($payload));
            $stream->rewind();
        }

        $env = Environment::mock([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $path,
        ]);

        $headersObj = new Headers();
        foreach ($headers as $name => $value) {
            $headersObj->addHeader($name, $value);
        }

        return new Request($method, $uri, $headersObj, [], $env->all(), $stream);
    }

    private function handleRequest(Request $request): Response
    {
        $response = new Response();
        return $this->app->handle($request);
    }

    public function testGetCurriculumsEndpoint()
    {
        $request = $this->createRequest('GET', '/curriculums');
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertJson($body);
    }

    public function testScheduleConflictEndpoint()
    {
        $request = $this->createRequest('GET', '/schedule/conflicts');
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertJson($body);
    }

    public function testForceScheduleOverride()
    {
        $payload = [
            'class_id' => 1,
            'override_conflict' => true,
            'reason' => 'Admin override for special session'
        ];

        $request = $this->createRequest('POST', '/schedule/force', ['Content-Type' => 'application/json'], $payload);
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertJson($body);
    }
}
