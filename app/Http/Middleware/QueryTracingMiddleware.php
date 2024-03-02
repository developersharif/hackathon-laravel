<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\OpenTelemetry\BaseTracer;

class QueryTracingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        DB::listen(function ($query) {

            /** @var BaseTracer $tracer */
            $tracer = BaseTracer::getTracer();
            $span = $tracer->spanBuilder("database tracing")->startSpan();
            $spanScope = $span->activate();

            $span->end();
            $spanScope->detach();

        });
        return $next($request);
    }
}
