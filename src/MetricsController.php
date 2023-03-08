<?php

declare(strict_types=1);

namespace JoeriAbbo\LaravelPrometheusExporter;

use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Prometheus\RenderTextFormat;
use Symfony\Component\HttpFoundation\Response;

class MetricsController extends Controller
{
    public function __construct(protected ResponseFactory $responseFactory, protected PrometheusExporter $prometheusExporter)
    {
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
