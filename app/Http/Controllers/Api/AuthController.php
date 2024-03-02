<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\OpenTelemetry\BaseTracer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validateUser = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }


        DB::beginTransaction();

        try {
            /** @var BaseTracer $tracer */
            $tracer = BaseTracer::getTracer();
            $span = $tracer->spanBuilder("Register User")->startSpan();
            $spanScope = $span->activate();

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = $request->input('password');
            $user->save();

            $span->end();

            $spanScope->detach();

            /** @var BaseTracer $tracer */
            $tracer = BaseTracer::getTracer();
            $span = $tracer->spanBuilder("Crate Bearer Token")->startSpan();
            $spanScope = $span->activate();

            $token = $user->createToken("API TOKEN")->plainTextToken;

            $span->end();

            $spanScope->detach();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'user' => $user,
                'token' => $token
            ], 200);


        } catch (\Exception $exception) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            /** @var BaseTracer $tracer */
            $tracer = BaseTracer::getTracer();
            $span = $tracer->spanBuilder("Login user")->startSpan();
            $spanScope = $span->activate();

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }


            $span->end();

            $spanScope->detach();


            $user = User::query()->where('email', $request->input('email'))->first();

            /** @var BaseTracer $tracer */
            $tracer = BaseTracer::getTracer();
            $span = $tracer->spanBuilder("Crate Bearer Token")->startSpan();
            $spanScope = $span->activate();


            $token = $user->createToken("API TOKEN")->plainTextToken;

            $span->end();

            $spanScope->detach();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'user' => $user,
                'token' => $token
            ], 200);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }
    public function logout(): \Illuminate\Http\JsonResponse
    {

        try {

            /** @var BaseTracer $tracer */
            $tracer = BaseTracer::getTracer();
            $span = $tracer->spanBuilder("Logout User")->startSpan();
            $spanScope = $span->activate();

            /** @var User $user */
            $user = auth()->user();
            $user->tokens()->delete();

            $span->end();

            $spanScope->detach();


            return response()->json([
                'status' => true,
                'message' => 'User Logout Successfully',
            ], 200);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
        }

    }

    public function verifyToken(string $token)
    {
        $token  = DB::table('personal_access_tokens')
            ->select(['token'])
            ->where('token',$token)
            ->first();
    }
}
