<?php

declare(strict_types=1);

namespace JoeriAbbo\LaravelPrometheusExporter\Tests;

use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use JoeriAbbo\LaravelPrometheusExporter\MetricsController;
use JoeriAbbo\LaravelPrometheusExporter\PrometheusExporter;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prometheus\RenderTextFormat;

#[CoversClass(\JoeriAbbo\LaravelPrometheusExporter\MetricsController::class)]
class MetricsControllerTest extends TestCase
{
    /**
     * @var ResponseFactory|Mockery\MockInterface
     */
    private $responseFactory;

    /**
     * @var PrometheusExporter|Mockery\MockInterface
     */
    private $exporter;

    /**
     * @var MetricsController
     */
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseFactory = Mockery::mock(ResponseFactory::class);
        $this->exporter = Mockery::mock(PrometheusExporter::class);
        $this->controller = new MetricsController($this->responseFactory, $this->exporter);
    }

    public function testGetMetrics(): void
    {
        $mockResponse = Mockery::mock(Response::class);
        $this->responseFactory->shouldReceive('make')
            ->once()
            ->withArgs([
                "\n",
                200,
                ['Content-Type' => RenderTextFormat::MIME_TYPE],
            ])
            ->andReturn($mockResponse);
        $this->exporter->shouldReceive('export')
            ->once()
            ->andReturn([]);

        $actualResponse = $this->controller->getMetrics();
        $this->assertSame($mockResponse, $actualResponse);
    }
}
