<?php

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Contrib\Zipkin\Exporter as ZipkinExporter;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Common\Export\Http\PsrTransportFactory;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

$httpClient = new Client();
$httpFactory = new HttpFactory();
$transportFactory = new PsrTransportFactory($httpClient, $httpFactory);

$defaultResource = ResourceInfoFactory::defaultResource();

$customResource = ResourceInfo::create(
    Attributes::create([
        'service.name' => 'order-service',
    ])
);

$resource = $defaultResource->merge($customResource);

$tracer = (new TracerProvider(
    [
        new SimpleSpanProcessor(
            new OpenTelemetry\Contrib\Zipkin\Exporter(
                $transportFactory->create('http://zipkin:9411/api/v2/spans', 'application/json')
            ),
        ),
    ],
    new AlwaysOnSampler(),
    $resource
))->getTracer('order-service');

$request = Request::capture();
$span = $tracer->spanBuilder($request->url())->startSpan();
$spanScope = $span->activate();

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest($request);

$span->end();
$spanScope->detach();
