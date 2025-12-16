<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Library API Documentation",
 *      description="API for managing books and borrow records.",
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="sanctum",
 *      type="apiKey",
 *      in="header",
 *      name="Authorization"
 * )
 */
class AuthController extends Controller

{
/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Register a new user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="password"),
 *             @OA\Property(property="password_confirmation", type="string", example="password")
 *         )
 *     ),
 *     @OA\Response(response=201, description="User registered successfully")
 * )
 */

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication endpoints"
 * )
 */

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users',
            'password'=>'required|string|min:6|confirmed',
            'role'=>'in:student,admin'
        ]);

        $user = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'role'=>$data['role'] ?? 'student'
        ]);
        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json(['user'=>$user,'token'=>$token],201);
    }
/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Login user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="password")
 *         )
 *     ),
 *     @OA\Response(response=200, description="User logged in")
 * )
 */

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user = User::where('email',$data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email'=>['The provided credentials are incorrect.']]);
        }

        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json(['user'=>$user,'token'=>$token],200);
    }

/**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Logout user",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="Logged out successfully")
 * )
 */

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out'],200);
    }
}
