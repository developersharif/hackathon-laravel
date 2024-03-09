<?php

namespace App\OpenTelemetry;

use OpenTelemetry\Contrib\Zipkin\Exporter as ZipkinExporter;
use OpenTelemetry\SDK\Common\Export\Http\PsrTransportFactory;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;


/**
 * @method spanBuilder(string $string)
 */
class BaseTracer
{
    public static function getTracer(): \OpenTelemetry\API\Trace\TracerInterface
    {
        return (new TracerProvider(
            [
                new SimpleSpanProcessor(
                    new ZipkinExporter(
                        PsrTransportFactory::discover()->create('http://localhost:9411/api/v2/spans', 'application/json')
                    ),
                ),
                new SimpleSpanProcessor(
                    new ZipkinExporter(
                        PsrTransportFactory::discover()->create('http://localhost:9412', 'application/json')
                    ),
                ),
            ],
            new AlwaysOnSampler(),
        ))->getTracer('Custom Instrument Laravel Application');
    }
}
