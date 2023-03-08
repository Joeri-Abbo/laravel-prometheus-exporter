<?php

declare(strict_types=1);

namespace JoeriAbbo\LaravelPrometheusExporter;

use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Prometheus\RenderTextFormat;
use Symfony\Component\HttpFoundation\Response;

class MetricsController extends Controller
{
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var PrometheusExporter
     */
    protected $prometheusExporter;

    public function __construct(ResponseFactory $responseFactory, PrometheusExporter $prometheusExporter)
    {
        $this->responseFactory = $responseFactory;
        $this->prometheusExporter = $prometheusExporter;
    }

    /**
     * GET /metrics
     *
     * The route path is configurable in the prometheus.metrics_route_path config var, or the
     * PROMETHEUS_METRICS_ROUTE_PATH env var.
     */
    public function getMetrics(): Response
    {
        $metrics = $this->prometheusExporter->export();

        $renderer = new RenderTextFormat();
        $result = $renderer->render($metrics);

        return $this->responseFactory->make($result, 200, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
    }
}