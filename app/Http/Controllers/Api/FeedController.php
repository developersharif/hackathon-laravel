<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\OpenTelemetry\BaseTracer;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        /** @var BaseTracer $tracer */
        $tracer = BaseTracer::getTracer();
        $span = $tracer->spanBuilder("Get Feeds")->startSpan();
        $spanScope = $span->activate();


        $span->end();

        $spanScope->detach();

        return response()->json(['feeds' => 'data']);
    }
}
