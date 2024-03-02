<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\OpenTelemetry\BaseTracer;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {

            /** @var BaseTracer $tracer */
            $tracer = BaseTracer::getTracer();
            $span = $tracer->spanBuilder("Get users")->startSpan();
            $spanScope = $span->activate();

            $users = User::query()->orderBy('id', 'desc')->paginate(20);

            $span->end();

            $spanScope->detach();

            return response()->json([
                'status' => true,
                'message' => 'User Retrieved Successfully',
                'users' => $users
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
