<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API untuk mengelola autentikasi pengguna"
 * )
 *
 * @OA\Schema(
 *     schema="Auth",
 *     title="Schema Auth",
 *     description="Schema user",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="johndoe@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-07T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-07T12:00:00Z")
 * )
 */

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/register",
     *      tags={"Authentication"},
     *      summary="Registrasi pengguna baru",
     *      description="Endpoint ini digunakan untuk mendaftarkan pengguna baru dan mengembalikan token akses.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email", "password"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Registrasi berhasil",
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string", example="1|qwertyuiop1234567890")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Registrasi gagal"),
     *      @OA\Response(response=500, description="Kesalahan server")
     * )
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json(['token' => $token], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/login",
     *      tags={"Authentication"},
     *      summary="Login pengguna",
     *      description="Endpoint ini digunakan untuk login dan mendapatkan token akses.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login berhasil",
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string", example="1|qwertyuiop1234567890")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Login gagal"),
     *      @OA\Response(response=500, description="Kesalahan server")
     * )
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                throw ValidationException::withMessages(['email' => 'Invalid credentials']);
            }

            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Login failed', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/logout",
     *      tags={"Authentication"},
     *      summary="Logout pengguna",
     *      description="Endpoint ini digunakan untuk logout dan menghapus token akses saat ini.",
     *      security={
     *          {"sanctum": {}}
     *      },
     *      @OA\Response(
     *          response=200,
     *          description="Logout berhasil",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Logged out successfully")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Logout gagal"),
     *      @OA\Response(response=500, description="Kesalahan server")
     * )
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Logout failed', 'message' => $e->getMessage()], 400);
        }
    }
}
