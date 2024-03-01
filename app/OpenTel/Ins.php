<?php

use OpenTelemetry\Sdk\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\Sdk\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\Sdk\Trace\TracerProvider;
use OpenTelemetry\Sdk\InstrumentationLibrary;
use OpenTelemetry\Instrumentation\Http\Client\HttpClientInstrumentor;
use OpenTelemetry\Instrumentation\Http\Server\HttpServerInstrumentor;
use OpenTelemetry\Instrumentation\Guzzle\GuzzleInstrumentor;
use OpenTelemetry\Instrumentation\Monolog\MonologInstrumentor;
use OpenTelemetry\Instrumentation\Prometheus\Exporter as PrometheusExporter;
use OpenTelemetry\Instrumentation\Grafana\Exporter as LokiExporter;
use OpenTelemetry\Instrumentation\Grafana\Exporter as TempoExporter;

$tracerProvider = new TracerProvider(
    new BatchSpanProcessor(
        new YourSpanExporter(),
        new AlwaysOnSampler()
    ),
    new InstrumentationLibrary('your-library-name', 'your-library-version')
);

HttpClientInstrumentor::enable();
HttpServerInstrumentor::enable();
GuzzleInstrumentor::enable();
MonologInstrumentor::enable();

// Prometheus Exporter
$prometheusExporter = new PrometheusExporter('laravel_service');
$tracerProvider->addSpanProcessor(new BatchSpanProcessor($prometheusExporter));

// Loki Exporter
$lokiExporter = new LokiExporter('http://loki:3100/loki/api/v1/push');
$tracerProvider->addSpanProcessor(new BatchSpanProcessor($lokiExporter));

// Tempo Exporter
$tempoExporter = new TempoExporter('http://tempo:3100');
$tracerProvider->addSpanProcessor(new BatchSpanProcessor($tempoExporter));

// Register the tracer provider globally
OpenTelemetry::setTracerProvider($tracerProvider);
