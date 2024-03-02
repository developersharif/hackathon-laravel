<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['feeds' => 'data']);
    }
}
