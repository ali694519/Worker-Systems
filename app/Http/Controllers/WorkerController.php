<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\WorkerStoreRequest;
use App\Models\Worker;
use App\Services\WorkerService\WorkerLoginService\WorkerLoginService;
use App\Services\WorkerService\WorkerRegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkerController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:worker', ['except' => ['login', 'register','verify']]);
    }
    /**
     * Get a JWT via given <credentials class=""></credentials>
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request){

        return (new WorkerLoginService())->login($request);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(WorkerStoreRequest $request) {

        return (new WorkerRegisterService)->register($request);
    }

   public function verify($token) {
        $worker = Worker::whereVerificationToken($token)->first();
        if(!$worker) {
            return response()->json([
                "message" => "this token is invalid"
            ]);
        }
        $worker->verification_token = null;
        $worker->verified_at = now();
        $worker->save();
        return response()->json([
            "message" => "Your account has been verified"
        ]);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->guard('worker')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->guard('worker')->user()
        ]);
    }
}
